<?php

/*.
    require_module 'standard';
.*/

namespace concom;

global $FUNCTIONDIR;

require_once($FUNCTIONDIR.'/users.inc');
require_once($FUNCTIONDIR.'/database.inc');

class ConcomList
{


    public static function listBuild($event = null)
    {
        global $Departments;

        if ($event === null) {
            $event = \current_eventID();
            if ($event === null) {
                return array();
            }
        }
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
                          'Note' => $value['Note'],
                          'Pronouns' => '');
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
                'Pronouns' => ''
                      ]];
            }

            if (!in_array($value['AccountID'], $ids)) {
                $ids[] = $value['AccountID'];
            }
            $value = $result->fetch();
        }

        if (!empty($ids)) {
            $users = lookup_users_by_ids($ids, ['Pronouns']);

            foreach ($users['users'] as $user) {
                if (array_key_exists($user['Id'], $db_staff)) {
                    foreach ($db_staff[$user['Id']] as $entry_key => $entry) {
                        if (array_key_exists('First Name', $user)) {
                            $db_staff[$user['Id']][$entry_key]['First Name'] = htmlspecialchars_decode($user['First Name'], ENT_QUOTES);
                        } else {
                            $db_staff[$user['Id']][$entry_key]['First Name'] = '';
                        }
                        if (array_key_exists('Last Name', $user)) {
                            $db_staff[$user['Id']][$entry_key]['Last Name'] = htmlspecialchars_decode($user['Last Name'], ENT_QUOTES);
                        } else {
                            $db_staff[$user['Id']][$entry_key]['Last Name'] = '';
                        }
                        $db_staff[$user['Id']][$entry_key]['Full Name'] = $db_staff[$user['Id']][$entry_key]['First Name'].'&nbsp;'.$db_staff[$user['Id']][$entry_key]['Last Name'];
                        if (array_key_exists('Email', $user)) {
                            $db_staff[$user['Id']][$entry_key]['Email'] = htmlspecialchars_decode($user['Email'], ENT_QUOTES);
                        } else {
                            $db_staff[$user['Id']][$entry_key]['Email'] = '';
                        }
                        if (array_key_exists('Pronouns', $user)) {
                            $db_staff[$user['Id']][$entry_key]['Pronouns'] = htmlspecialchars_decode($user['Pronouns'], ENT_QUOTES);
                        } else {
                            $db_staff[$user['Id']][$entry_key]['Pronouns'] = '';
                        }
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
