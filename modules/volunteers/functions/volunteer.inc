<?php

/*.
    require_module 'standard';
.*/

require_once($FUNCTIONDIR."/database.inc");
require_once($FUNCTIONDIR."/functions.inc");
require_once($FUNCTIONDIR."/users.inc");


function get_volunteer_hours_for_user($id, $event = null)
{
    if ($event === null) {
        $event = current_eventID();
    }
    $user = lookup_user_by_id($id);
    $sql = <<<SQL
        SELECT v.*,
            (SELECT Name FROM Departments
                WHERE DepartmentID = v.DepartmentID) as DepartmentName
        FROM VolunteerHours v, Departments d
        WHERE v.AccountId = $id AND v.DepartmentID = d.DepartmentID AND
              v.EventID = $event;
SQL;

    $data = array();
    $result = DB::run($sql);
    $value = $result->fetch();
    while ($value !== false) {
        $data[] = [
        'EntryID' => $value['HourEntryID'],
        'Volunteer' => $user['users'][0]['First Name'],
        'Actual Hours' => $value['ActualHours'],
        'Time Modifier' => $value['TimeModifier'],
        'End Date Time' => $value['EndDateTime'],
        'Department Worked' => $value['DepartmentName'],
        'Department ID' => $value['DepartmentID'],
        'Entered By' => $value['EnteredByID'],
        'Authorized By' => $value['AuthorizedByID'],
        ];
        $value = $result->fetch();
    }

    return $data;

}


function record_volunteer_hours(
    $id,
    $hours,
    $end,
    $modifier,
    $department,
    $enterer,
    $authorized,
    $event = null
) {
    if ($event === null) {
        /* Convention ... assume most recent one */
        $event = current_eventID();
    }

    /* Get the department id from name if it is a name */
    if (!is_int($department)) {
        $sql = "SELECT DepartmentID FROM Departments WHERE Name = '$department';";
        $result = DB::run($sql);
        $value = $result->fetch();
        if ($value) {
            $departmentID = $value['DepartmentID'];
        } else {
            $departmentID = 0;
        }
    } else {
        $departmentID = $department;
    }

    $sql = <<<SQL
        INSERT INTO VolunteerHours
            (AccountID, ActualHours, EndDateTime, TimeModifier,
             DepartmentID, EnteredByID, AuthorizedByID, EventID)
        VALUES ($id, $hours, '$end', $modifier, $departmentID, $enterer,
                $authorized, $event);
SQL;

    $result = DB::run($sql);
    if ($result === false) {
        return PDO::errorInfo();
    }
    return null;

}


function update_volunteer_hours(
    $record_id,
    $user = null,
    $hours = null,
    $end = null,
    $modifier = null,
    $department = null,
    $enterer = null,
    $authorized = null,
    $event = null
) {
    if ($user === null && $hours === null && $end === null &&
        $modifier === null && $department === null && $enterer === null &&
        $authorized === null && $event === null) {
        return null;
    }

    $departmentID = null;

    /* Get the department id from name if it is a name */
    if ($department !== null) {
        if (!is_int($department)) {
            $sql = "SELECT `DepartmentID` FROM Departments WHERE Name = ".MyPDO::quote($department).";";
            $result = DB::run($sql);
            $value = $result->fetch();
            if ($value) {
                $departmentID = $value['DepartmentID'];
            } else {
                $departmentID = 0;
            }
        } else {
            $departmentID = $department;
        }
    }

    $sql = "UPDATE `VolunteerHours` SET";
    $t = [];
    if ($user !== null) {
        $t[] = " AccountID = ".$user;
    }
    if ($hours !== null) {
        $t[] = " ActualHours = ".$hours;
    }
    if ($end !== null) {
        $t[] = " EndDateTime = '".$end."'";
    }
    if ($modifier !== null) {
        $t[] = " TimeModifier = ".$modifier;
    }
    if ($departmentID !== null) {
        $t[] = " DepartmentID = ".$departmentID;
    }
    if ($enterer !== null) {
        $t[] = " EnteredByID = ".$enterer;
    }
    if ($authorized !== null) {
        $t[] = " AuthorizedByID = ".$authorized;
    }
    if ($event !== null) {
        $t[] = " EventID = ".$event;
    }
    $sql .= implode(',', $t);
    $sql .= " WHERE HourEntryID = ";
    $sql .= $record_id;

    $result = DB::run($sql);
    if ($result === false) {
        return PDO::errorInfo();
    }
    return null;

}


function delete_volunteer_hours($record_id)
{
    $sql = <<<SQL
        DELETE FROM `VolunteerHours` WHERE `HourEntryID` = $record_id;
SQL;

    DB::run($sql);

}


function add_volunteer_prize_group($limit = 1)
{
    $sql = "INSERT INTO `RewardGroup` (RedeemLimit) VALUES($limit);";
    $result = DB::run($sql);

    $sql = "SELECT RewardGroupID FROM `RewardGroup` ORDER BY RewardGroupID DESC LIMIT 1;";
    $result = DB::run($sql);
    $value = $result->fetch();
    return $value['RewardGroupID'];

}


function update_volunteer_prize_group($group_id, $limit)
{
    $sql = <<<SQL
        UPDATE `RewardGroup`
        SET RedeemLimit = $limit
        WHERE RewardGroupID = $group_id;
SQL;
    DB::run($sql);

}


function award_prizes($id, $prizes, $event = null)
{
    if ($event === null) {
        $event = current_eventID();
    }

    $sql = <<<SQL
        INSERT INTO HourRedemptions
            (AccountID, PrizeID, EventID)
            VALUES
SQL;
    $stmt = [];
    foreach ($prizes as $prize) {
        $stmt[] = " ($id, $prize, $event)";
    }
    $sql .= implode(',', $stmt);
    $sql .= ";";
    DB::run($sql);
    return true;

}


function update_volunteer_prize(
    $record_id,
    $name = null,
    $value = null,
    $promo = null,
    $group = null,
    $total = null
) {
    if ($name === null && $value === null && $promo === null &&
        $group === null && $total === null) {
        return null;
    }

    $sql = "UPDATE `VolunteerRewards` SET";
    $t = [];
    if ($name !== null) {
        $t[] = " Name = ".MyPDO::quote($name);
    }
    if ($value !== null) {
        $t[] = " Value = ".$value;
    }
    if ($promo !== null) {
        if ($promo) {
            $t[] = " Promo = 1";
        } else {
            $t[] = " Promo = 0";
        }
    }
    if ($group !== null) {
        if ($group == '') {
            $t[] = " RewardGroupID = NULL";
        } else {
            $t[] = " RewardGroupID = ".$group;
        }
    }
    if ($total !== null) {
        $t[] = " TotalInventory = ".$total;
    }

    $sql .= implode(',', $t);
    $sql .= " WHERE PrizeID = ";
    $sql .= $record_id;

    $result = DB::run($sql);
    if ($result === false) {
        return PDO::errorInfo();
    }
    return null;

}


function delete_volunteer_prize($record_id)
{
    $sql = <<<SQL
        DELETE FROM `VolunteerRewards` WHERE `PrizeID` = $record_id;
SQL;

    DB::run($sql);

}


function new_volunteer_prize(
    $name,
    $value,
    $promo,
    $group,
    $total
) {
    if ($promo) {
        $promo = 1;
    } else {
        $promo = 0;
    }
    if ($group == null || $group == '') {
        $group = 'NULL';
    }
    $name = MyPDO::quote($name);
    $sql = <<<SQL
            INSERT INTO `VolunteerRewards`
                (Name, Value, Promo, RewardGroupID, TotalInventory)
            VALUES ($name, $value, $promo, $group, $total);
SQL;
    DB::run($sql);

}


function volunteer_minimum_hour_report($required_hours, $event = null)
{
    global $ConComHours;

    if ($event === null) {
        $event = current_eventID();
    }

    $members = [];
    $hours = [];
    $sql = <<<SQL
        SELECT
            AccountID,
            SUM(ActualHours * TimeModifier) AS TotalHours
        FROM
            `VolunteerHours`
        WHERE
            EventID = $event
        Group by
            AccountID
        order BY
            TotalHours DESC;
SQL;
    $result = \DB::run($sql);
    $value = $result->fetch();
    while ($value !== false) {
        if ($value['TotalHours'] < $required_hours) {
            break;
        }
        $members[] = $value['AccountID'];
        $hours[$value['AccountID']] = $value['TotalHours'];
        $value = $result->fetch();
    }

    if (class_exists('\\concom\\ConcomList') &&
        method_exists('\\concom\\ConcomList', 'listBuild') &&
        isset($ConComHours) && $ConComHours >= $required_hours) {
        $concom = \concom\ConcomList::listBuild($event);

        foreach ($concom as $entry) {
            $members[] = $entry['Account ID'];
            $hours[$entry['Account ID']] = $ConComHours;
        }
    }

    if (!empty($members)) {
        $users = lookup_users_by_ids($members);
    }

    $report = [];
    foreach ($users['users'] as $user) {
        $report[] = ['Id' => $user['Id'],
        'Name' => $user['First Name'].' '.$user['Last Name'],
        'Email' => $user['Email'],
        'Hours' => $hours[$user['Id']]];
    }

    return $report;

}


function volunteers_in_dept($department, $event = null)
{
    if ($event === null) {
        $event = current_eventID();
    }

    $conditional = "";
    if (is_array($event)) {
        if (!empty($event)) {
            $conditional = "AND EventID IN (".implode(', ', $event).")";
        }
    } else {
        $conditional = "AND EventID = ".strval($event);
    }

    /* Get the department id from name if it is a name */
    if (!is_int($department)) {
        $sql = "SELECT DepartmentID FROM Departments WHERE Name = '$department';";
        $result = DB::run($sql);
        $value = $result->fetch();
        if ($value) {
            $departmentID = $value['DepartmentID'];
        } else {
            $departmentID = 0;
        }
    } else {
        $departmentID = $department;
    }

    $sql = <<<SQL
        SELECT
            AccountID,
            SUM(ActualHours * TimeModifier) AS TotalHours
        FROM
            `VolunteerHours`
        WHERE
            DepartmentID = $departmentID
            $conditional
        Group by
            AccountID,
            DepartmentID
SQL;

    $result = DB::run($sql);
    $value = $result->fetch();
    while ($value !== false) {
        $members[] = $value['AccountID'];
        $hours[$value['AccountID']] = $value['TotalHours'];
        $value = $result->fetch();
    }

    $users = lookup_users_by_ids($members);

    $report = [];
    foreach ($users['users'] as $user) {
        $report[] = ['Id' => $user['Id'],
        'Name' => $user['First Name'].' '.$user['Last Name'],
        'Email' => $user['Email'],
        'Hours' => $hours[$user['Id']]];
    }

    return $report;

}


function refund_prizes($id, $prizes, $event = null)
{
    if ($event === null) {
        $event = current_eventID();
    }

    $stmt = [];
    foreach ($prizes as $prize) {
        $sql = <<<SQL
            DELETE FROM
                `HourRedemptions`
            WHERE
                `AccountID` = $id
                AND EventId = $event
                AND prizeid = $prize
            LIMIT
                1
SQL;
        DB::run($sql);
    }
    return true;

}


function find_overlap_hours($endtime, $hours, $uid)
{
    $checkend = strtotime($endtime);
    $checkstart = $checkend - (intval($hours) * 3600);

    $sql = <<<SQL
    SELECT
        *,
        UNIX_TIMESTAMP(`EndDateTime`) - (`ActualHours` * 3600) AS StartTime,
        UNIX_TIMESTAMP(`EndDateTime`) AS EndTime
    FROM
        `VolunteerHours`
    WHERE
        `AccountID` = $uid
    HAVING
        ($checkstart >= `StartTime` AND $checkstart < `EndTime`) OR
        ($checkend <= `EndTime` AND $checkend > `StartTime`) OR
        ($checkstart <= `StartTime` AND $checkend >= `EndTime`)
SQL;
    $result = DB::run($sql);
    $value = $result->fetch();
    if ($value !== false) {
        return $value;
    } else {
        return null;
    }

}
