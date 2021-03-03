<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\staff\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

class ListStaff extends BaseStaff
{


    private function listBuild($event)
    {
        global $Departments;

        if ($event === null) {
            return array();
        }
        $sql = <<<SQL
    SELECT
        l.ListRecordID,
        l.AccountID,
        COALESCE(l.Note, "") AS Note,
        (
            SELECT
                Name
            FROM
                Departments
            WHERE
                DepartmentID = l.DepartmentID
        ) AS Department,
        (
            SELECT
                (
                    CASE WHEN DepartmentID = ParentDepartmentID THEN 1 ELSE 0 END
                )
            FROM
                Departments
            WHERE
                DepartmentID = l.DepartmentID
        ) AS Divisional,
        (
            SELECT
                Name
            FROM
                ConComPositions
            WHERE
                PositionID = l.PositionID
        ) AS Position,
        (
            SELECT
                EventName
            FROM
                Events
            WHERE
                EventID = l.EventID
        ) AS Event
    FROM
        ConComList AS l
    WHERE
        l.EventID = $event
        AND l.DepartmentID NOT IN (
            SELECT
                `DepartmentID`
            FROM
                `Departments`
            WHERE
                Name = 'Historical Placeholder'
        )
        AND l.DepartmentID NOT IN (
            SELECT
                `DepartmentID`
            FROM
                `Departments`
            WHERE
                ParentDepartmentID IN (
                    SELECT
                        `DepartmentID`
                    FROM
                        `Departments`
                    WHERE
                        Name = 'Historical Placeholder'
                )
        )
SQL;

        $ids = array();
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $value = $sth->fetch();
        $db_staff = array();
        while ($value !== false) {
            $Division = $Departments[$value['Department']]['Division'];
            if ($value['Divisional']) {
                if ($value['Position'] == "Head") {
                    $value['Department'] = "Division Director";
                    $value['Position'] = "Director";
                } elseif ($value['Position'] == "Specialist") {
                    $value['Department'] = "Division Support";
                }
            }

            if (array_key_exists($value['AccountID'], $db_staff)) {
                $entry = array('Account ID' => $value['AccountID'],
                          'ListRecordID' => $value['ListRecordID'],
                          'Division' => $Division,
                          'Department' => $value['Department'],
                          'Position' => $value['Position'],
                          'Email' => '',
                          'First Name' => $value['AccountID'],
                          'Last Name' => '',
                          'Note' => $value['Note']);
                array_push($db_staff[$value['AccountID']], $entry);
            } else {
                $db_staff[$value['AccountID']] = [[
                'ListRecordID' => $value['ListRecordID'],
                'Account ID' => $value['AccountID'],
                'Division' => $Division,
                'Department' => $value['Department'],
                'Position' => $value['Position'],
                'Email' => '',
                'First Name' => $value['AccountID'],
                'Last Name' => '',
                'Note' => $value['Note'],
                      ]];
            }

            if (!in_array($value['AccountID'], $ids)) {
                $ids[] = $value['AccountID'];
            }
            $value = $sth->fetch();
        }

        if (!empty($ids)) {
            $users = \lookup_users_by_ids($ids);

            foreach ($users['users'] as $user) {
                if (array_key_exists($user['Id'], $db_staff)) {
                    foreach ($db_staff[$user['Id']] as $entry_key => $entry) {
                        $db_staff[$user['Id']][$entry_key]['First Name'] = $user['First Name'];
                        $db_staff[$user['Id']][$entry_key]['Last Name'] = $user['Last Name'];
                        $db_staff[$user['Id']][$entry_key]['Full Name'] = $user['First Name'].'&nbsp;'.$user['Last Name'];
                        $db_staff[$user['Id']][$entry_key]['Email'] = $user['Email'];
                    }
                }
            }
        }

        $output = [];
        // Flatten our multi-dimentional array
        foreach ($db_staff as $staff) {
            foreach ($staff as $entry) {
                $output[] = $entry;
            }
        }

        return $output;

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.get.staff'];
        $this->checkPermissions($permissions);
        if (array_key_exists('event', $params)) {
            $event = $params['event'];
        } else {
            $event = \current_eventID();
        }

        $target = new \App\Controller\Event\GetEvent($this->container);
        $target->buildResource($request, $response, ['id' => $event])[1];

        $staff = $this->listBuild($event);
        $data = array();
        foreach ($staff as $entry) {
            $dept = $this->getDepartment($entry['Department']);
            if ($dept) {
                $id = $dept['id'];
            } else {
                $id = $entry['Department'];
            }
            $data[] = $this->buildEntry($request, $entry['ListRecordID'], $id, $entry['Account ID'], $entry['Note'], $entry['Position']);
        }
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        array( 'type' => 'staff_list', 'event' => $event )
        ];

    }


    /* end ListStaff */
}
