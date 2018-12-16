<?php

/*.
    require_module 'standard';
.*/

namespace concom;

require_once($FUNCTIONDIR.'/users.inc');
require_once($FUNCTIONDIR.'/database.inc');

class ConcomList
{


    public static function listBuild()
    {
        global $Departments;

        $event = \current_eventID();
        $sql = <<<SQL
    SELECT
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
SQL;

        $ids = array();
        $result = \DB::run($sql);
        $value = $result->fetch();
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
            $value = $result->fetch();
        }

        if (!empty($ids)) {
            $str = "('".implode("', '", $ids)."')";
            $users = lookup_users_by_ids($str);

            foreach ($users['users'] as $user) {
                if (array_key_exists($user['Id'], $db_staff)) {
                    foreach ($db_staff[$user['Id']] as $entry_key => $entry) {
                        $db_staff[$user['Id']][$entry_key]['First Name'] = $user['First Name'];
                        $db_staff[$user['Id']][$entry_key]['Last Name'] = $user['Last Name'];
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


    /* end CONCOM_LIST */
}
