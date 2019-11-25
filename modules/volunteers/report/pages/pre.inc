<?php

/*.
    require_module 'standard';
.*/

/* This should only be accessable from volunteer staff */
if (!(isset($_SESSION['IS_ADMIN']) || $_SESSION['IS_VOLUNTEERS'])) {
    if (empty($_SESSION['customFields']['currConComPos'])) {
        goSite();
    }
}


$reports = [
'Volunteer Rewards' => 'SELECT r.*, (SELECT COUNT(h.PrizeID) FROM HourRedemptions AS h WHERE h.PrizeID = r.PrizeID GROUP BY h.PrizeID) as Claimed FROM VolunteerRewards as r;',
'Volunteer Hours' => 'SELECT v.*, (SELECT Name FROM `Departments` as d WHERE v.DepartmentID = d.DepartmentID) as DepartmentName FROM `VolunteerHours` as v;',
'Prize Redemption' => 'SELECT *, (SELECT Name FROM VolunteerRewards WHERE VolunteerRewards.prizeid = HourRedemptions.PrizeID) AS PrizeName FROM `HourRedemptions` ORDER BY AccountID'
];


if (!empty($_REQUEST)) {
    // Retrieve and sanitize GET data
    $arguments = [
    'min_hour'         => FILTER_SANITIZE_NUMBER_INT,
    'dept_report'      => FILTER_SANITIZE_NUMBER_INT,
    'dept_name'        => FILTER_SANITIZE_SPECIAL_CHARS,
    'generateCSV'      => FILTER_SANITIZE_SPECIAL_CHARS,
    'report'           => FILTER_SANITIZE_SPECIAL_CHARS,
    'name'             => FILTER_SANITIZE_SPECIAL_CHARS,
    ];

    require_once($FUNCTIONDIR."/volunteer.inc");
    require_once($FUNCTIONDIR."/database.inc");

    $updateData = filter_input_array(INPUT_GET, $arguments);

    if (isset($updateData['min_hour'])) {
        if (isset($_SESSION['IS_ADMIN']) || $_SESSION['IS_VOLUNTEERS']) {
            $min = $updateData['min_hour'];
            $report = volunteer_minimum_hour_report($min);
            output_csv($report, "hour_report.csv");
            exit();
        } else {
            header("HTTP/1.0 401");
            exit();
        }
    }

    if (isset($updateData['dept_report'])) {
        if (isset($_SESSION['IS_ADMIN']) || $_SESSION['IS_VOLUNTEERS']) {
            $dept = $updateData['dept_report'];
            $name = $updateData['dept_name'];
            $event = current_eventID();
            $report = volunteers_in_dept(intval($dept), $event);
            output_csv($report, "department_report_".$name."_".$event.".csv");
            exit();
        } else {
            header("HTTP/1.0 401");
            exit();
        }
    }

    if (isset($updateData['generateCSV'])) {
        if (isset($_SESSION['IS_ADMIN']) || $_SESSION['IS_VOLUNTEERS']) {
            csv_headers($updateData['generateCSV'].'.csv');
            print dump_table_to_csv($updateData['generateCSV']);
            exit();
        }
    }

    if (isset($updateData['report'])) {
        if (isset($_SESSION['IS_ADMIN']) || $_SESSION['IS_VOLUNTEERS']) {
            csv_headers($updateData['report'].'.csv');
            print dump_sql_to_csv($reports[$updateData['report']], true);
            exit();
        }
    }
}
