<?php

/*.
    require_module 'standard';
    require_module 'json';
.*/

require_once($BACKEND."/RBAC.inc");

/* This should only be accessable from volunteer staff */
if (!(\ciab\RBAC::havePermission("volunteers.reports") ||
      $_SESSION['IS_VOLUNTEERS'])) {
    goSite();
}

require_once($FUNCTIONDIR."/reports.inc");

$reports = [
'Volunteer Rewards' => 'SELECT r.*, (SELECT COUNT(h.PrizeID) FROM HourRedemptions AS h WHERE h.PrizeID = r.PrizeID GROUP BY h.PrizeID) as Claimed FROM VolunteerRewards as r;',
'Volunteer Hours' => 'SELECT v.*, (SELECT Name FROM `Departments` as d WHERE v.DepartmentID = d.DepartmentID) as DepartmentName FROM `VolunteerHours` as v WHERE v.EventID = ( SELECT MAX(EventID) FROM Events );',
'Prize Redemption' => 'SELECT *, (SELECT Name FROM VolunteerRewards WHERE VolunteerRewards.prizeid = HourRedemptions.PrizeID) AS PrizeName FROM `HourRedemptions` WHERE EventID = ( SELECT MAX(EventID) FROM Events ) ORDER BY AccountID',
'Reward Group Table' => 'RewardGroup',
'Hour Redemptions Table' => 'HourRedemptions',
'Volunteer Hours Table' => 'VolunteerHours',
'Volunteer Rewards Table' => 'VolunteerRewards',
];

handle_report_request($reports);

if (!empty($_REQUEST)) {
    // Retrieve and sanitize GET data
    $arguments = [
    'min_hour'         => FILTER_SANITIZE_NUMBER_INT,
    'dept_report'      => FILTER_SANITIZE_NUMBER_INT,
    'dept_name'        => FILTER_SANITIZE_SPECIAL_CHARS,
    'name'             => FILTER_SANITIZE_SPECIAL_CHARS,
    ];

    require_once(__DIR__.'/../../functions/volunteer.inc');
    require_once($FUNCTIONDIR."/database.inc");

    $updateData = filter_input_array(INPUT_GET, $arguments);

    if (isset($updateData['min_hour'])) {
        print_csv_report(
            'hour_report.csv',
            volunteer_minimum_hour_report,
            $updateData['min_hour']
        );
        exit();
    }

    if (isset($updateData['dept_report'])) {
        $dept = $updateData['dept_report'];
        $name = $updateData['dept_name'];
        $event = current_eventID();
        print_csv_report(
            "department_report_".$name."_".$event.".csv",
            volunteers_in_dept,
            intval($dept),
            $event
        );
        exit();
    }
}
