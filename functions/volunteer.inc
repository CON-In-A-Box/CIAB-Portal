<?php

/*.
    require_module 'standard';
.*/

require_once(__DIR__."/database.inc");
require_once(__DIR__."/functions.inc");
require_once(__DIR__."/users.inc");


function get_volunteer_hour_summary($id = null, $year = null)
{
    global $db;

    if ($year === null) {
        $year = current_yearID();
    }

    if ($id !== null) {
        $sql = <<<SQL
              SELECT DepartmentID, COUNT(HourEntryID) AS EntryCount,
                     SUM(ActualHours * TimeModifier) AS TotalHours,
                     COUNT(DISTINCT AccountID) AS VolunteerCount,
                     (SELECT Name FROM Departments
                        WHERE DepartmentID = v.DepartmentID) AS DepartmentName
              FROM VolunteerHours v
              WHERE v.YearID = $year AND v.AccountID = $id
              GROUP BY DepartmentID ORDER BY DepartmentID;
SQL;
    } else {
        $sql = <<<SQL
              SELECT DepartmentID, COUNT(HourEntryID) AS EntryCount,
                     SUM(ActualHours * TimeModifier) AS TotalHours,
                     COUNT(DISTINCT AccountID) AS VolunteerCount,
                     (SELECT Name FROM Departments
                        WHERE DepartmentID = v.DepartmentID) AS DepartmentName
              FROM VolunteerHours v
              WHERE v.YearID = $year
              GROUP BY DepartmentID ORDER BY DepartmentID;
SQL;
    }

    $data = array();
    $result = $db->run($sql);
    $value = $result->fetch();
    while ($value !== false) {
        $data[] = [
        'Department Worked' => $value['DepartmentName'],
        'Department ID' => $value['DepartmentID'],
        'Total Entries' => $value['EntryCount'],
        'Total Volunteers' => $value['VolunteerCount'],
        'Total Hours' => $value['TotalHours'],
        ];
        $value = $result->fetch();
    }

    return $data;

}


function get_volunteer_year_summery($year = null)
{
    global $db;

    if ($year === null) {
        $year = current_yearID();
    }

    $sql = <<<SQL
        SELECT
            SUM(ActualHours * TimeModifier) AS TotalHours,
            COUNT(DISTINCT AccountID) AS VolunteerCount,
            (
                SELECT
                    COUNT(ClaimID) * (
                        SELECT
                            value
                        FROM
                            VolunteerRewards v
                        WHERE
                            v.prizeID = h.prizeID
                    )
                FROM
                    `HourRedemptions` as h
                WHERE
                    YearID = $year
                GROUP BY
                    YearID
            ) as TotalHoursSpent
        FROM
            VolunteerHours
        WHERE
            YearID = $year
        GROUP BY
            YearID;
SQL;

    $result = $db->run($sql);
    $value = $result->fetch();

    $output = [
    'TotalHours' => 0,
    'VolunteerCount' => 0,
    'TotalHoursSpent' => 0,
    ];

    if ($value['TotalHours']) {
        $output['TotalHours'] = $value['TotalHours'];
    }
    if ($value['VolunteerCount']) {
        $output['VolunteerCount'] = $value['VolunteerCount'];
    }
    if ($value['TotalHoursSpent']) {
        $output['TotalHoursSpent'] = $value['TotalHoursSpent'];
    }

    return $output;

}


function get_volunteer_hours_for_user($id, $year = null)
{
    global $db;

    if ($year === null) {
        $year = current_yearID();
    }
    $user = lookup_user_by_id($id);
    $sql = <<<SQL
        SELECT v.*,
            (SELECT Name FROM Departments
                WHERE DepartmentID = v.DepartmentID) as DepartmentName
        FROM VolunteerHours v, Departments d
        WHERE v.AccountId = $id AND v.DepartmentID = d.DepartmentID AND
              v.YearID = $year;
SQL;

    $data = array();
    $result = $db->run($sql);
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
    $year = null
) {
    global $db;

    if ($year === null) {
        /* Convention year... assume most recent one */
        $year = current_yearID();
    }

    /* Get the department id from name if it is a name */
    if (!is_int($department)) {
        $sql = "SELECT DepartmentID FROM Departments WHERE Name = '$department';";
        $result = $db->run($sql);
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
             DepartmentID, EnteredByID, AuthorizedByID, YearID)
        VALUES ($id, $hours, '$end', $modifier, $departmentID, $enterer,
                $authorized, $year);
SQL;

    $result = $db->run($sql);
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
    $year = null
) {
    global $db;

    if ($user === null && $hours === null && $end === null &&
        $modifier === null && $department === null && $enterer === null &&
        $authorized === null && $year === null) {
        return null;
    }

    $departmentID = null;

    /* Get the department id from name if it is a name */
    if ($department !== null) {
        if (!is_int($department)) {
            $sql = "SELECT `DepartmentID` FROM Departments WHERE Name = ".MyPDO::quote($department).";";
            $result = $db->run($sql);
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
    if ($year !== null) {
        $t[] = " YearID = ".$year;
    }
    $sql .= implode(',', $t);
    $sql .= " WHERE HourEntryID = ";
    $sql .= $record_id;

    $result = $db->run($sql);
    if ($result === false) {
        return PDO::errorInfo();
    }
    return null;

}


function delete_volunteer_hours($record_id)
{
    global $db;

    $sql = <<<SQL
        DELETE FROM `VolunteerHours` WHERE `HourEntryID` = $record_id;
SQL;

    $db->run($sql);

}


function volunteer_prizes()
{
    global $db;

    $groups = array();
    $sql = " SELECT * FROM RewardGroup;";
    $result = $db->run($sql);
    $value = $result->fetch();
    while ($value !== false) {
        $groups[$value['RewardGroupID']] = $value;
        $value = $result->fetch();
    }

    $prizes = array();
    $sql = <<<SQL
        SELECT * FROM VolunteerRewards
        ORDER BY Promo Desc, Value Asc, Name Desc;
SQL;

    $result = $db->run($sql);
    $value = $result->fetch();
    while ($value !== false) {
        $prizes[$value['PrizeID']] = $value;
        $prizes[$value['PrizeID']]['Remaining'] = $value['TotalInventory'];
        if ($prizes[$value['PrizeID']]['Promo']) {
            $prizes[$value['PrizeID']]['Promo'] = 'yes';
            $prizes[$value['PrizeID']]['Limit'] = 1;
        } else {
            $prizes[$value['PrizeID']]['Promo'] = 'no';
            if ($value['RewardGroupID'] != null) {
                $prizes[$value['PrizeID']]['Limit'] = $groups[$value['RewardGroupID']]['RedeemLimit'];
            } else {
                $prizes[$value['PrizeID']]['Limit'] = '∞';
            }
        }
        $value = $result->fetch();
    }

    $keys = array_keys($prizes);
    $sql = "SELECT PrizeID, COUNT(PrizeID) as Claimed FROM HourRedemptions GROUP BY PrizeID;";
    $result = $db->run($sql);
    $value = $result->fetch();
    while ($value !== false) {
        $prizes[$value['PrizeID']]['Remaining'] = $prizes[$value['PrizeID']]['TotalInventory'] - $value['Claimed'];
        $value = $result->fetch();
    }

    return $prizes;

}


function volunteer_prize_groups()
{
    global $db;

    $groups = array();
    $sql = " SELECT * FROM `RewardGroup`;";
    $result = $db->run($sql);
    $value = $result->fetch();
    while ($value !== false) {
        $groups[$value['RewardGroupID']] = $value['RedeemLimit'];
        $value = $result->fetch();
    }
    return $groups;

}


function add_volunteer_prize_group($limit = 1)
{
    global $db;

    $sql = "INSERT INTO `RewardGroup` (RedeemLimit) VALUES($limit);";
    $result = $db->run($sql);

    $sql = "SELECT RewardGroupID FROM `RewardGroup` ORDER BY RewardGroupID DESC LIMIT 1;";
    $result = $db->run($sql);
    $value = $result->fetch();
    return $value['RewardGroupID'];

}


function update_volunteer_prize_group($group_id, $limit)
{
    global $db;

    $sql = <<<SQL
        UPDATE `RewardGroup`
        SET RedeemLimit = $limit
        WHERE RewardGroupID = $group_id;
SQL;
    $db->run($sql);

}


function volunteer_prizes_for_user($id, $year = null)
{
    global $db;

    $data = volunteer_prizes();

    if ($year === null) {
        $year = current_yearID();
    }

    $sql = <<<SQL
            SELECT h.PrizeID, SUM(Value) as Value, COUNT(Value) AS Claimed
            FROM HourRedemptions AS h, VolunteerRewards AS r
            WHERE h.YearID = $year  AND h.AccountID = $id AND
                  r.PrizeID = h.PrizeID
            GROUP BY h.PrizeID;
SQL;

    $result = $db->run($sql);
    $value = $result->fetch();
    while ($value !== false) {
        $data[$value['PrizeID']]['Aquired'] = $value['Claimed'];
        $value = $result->fetch();
    }

    return $data;

}


function award_prizes($id, $prizes, $year = null)
{
    global $db;

    if ($year === null) {
        $year = current_yearID();
    }

    $sql = <<<SQL
        INSERT INTO HourRedemptions
            (AccountID, PrizeID, YearID)
            VALUES
SQL;
    $stmt = [];
    foreach ($prizes as $prize) {
        $stmt[] = " ($id, $prize, $year)";
    }
    $sql .= implode(',', $stmt);
    $sql .= ";";
    $db->run($sql);
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
    global $db;

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

    $result = $db->run($sql);
    if ($result === false) {
        return PDO::errorInfo();
    }
    return null;

}


function delete_volunteer_prize($record_id)
{
    global $db;

    $sql = <<<SQL
        DELETE FROM `VolunteerRewards` WHERE `PrizeID` = $record_id;
SQL;

    $db->run($sql);

}
