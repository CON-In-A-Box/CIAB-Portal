<?php

/*.
    require_module 'standard';
    require_module 'json';
.*/


/* This should only be accessable from volunteer staff */
if (!(boolval($_SESSION['IS_VOLUNTEERS']))) {
    PageAllowedByRBAC('volunteers.reports');
}

require_once($FUNCTIONDIR."/reports.inc");

$reports_fmt = [
'Volunteer Rewards for %event%' => 'SELECT r.*, (SELECT COUNT(h.PrizeID) FROM HourRedemptions AS h WHERE h.PrizeID = r.PrizeID AND h.EventID = \'%event%\' GROUP BY h.PrizeID) as Claimed FROM VolunteerRewards as r;',
'Volunteer Hours for %event%' => 'SELECT v.*, (SELECT Name FROM `Departments` as d WHERE v.DepartmentID = d.DepartmentID) as DepartmentName FROM `VolunteerHours` as v WHERE v.EventID = \'%event%\';',
'Prize Redemption for %event%' => 'SELECT *, (SELECT Name FROM VolunteerRewards WHERE VolunteerRewards.prizeid = HourRedemptions.PrizeID) AS PrizeName FROM `HourRedemptions` WHERE EventID = \'%event%\' ORDER BY AccountID'
];

$report_static = [
'Reward Group Table' => 'RewardGroup',
'Hour Redemptions Table' => 'HourRedemptions',
'Volunteer Hours Table' => 'VolunteerHours',
'Volunteer Rewards Table' => 'VolunteerRewards',
];

$reports = [];

$sql = 'SELECT * FROM `Events` ORDER BY `DateFrom` DESC;';
$result = \DB::Run($sql);
$value = $result->fetchAll();

foreach ($value as $event) {
    foreach ($reports_fmt as $title => $sql) {
        $reports[str_replace('%event%', $event['EventName'], $title)] = str_replace('%event%', $event['EventID'], $sql);
    }
}

handle_report_request(array_merge($reports, $report_static));

if (!empty($_REQUEST)) {
    // Retrieve and sanitize GET data
    $arguments = [
    'min_hour'         => FILTER_SANITIZE_NUMBER_INT,
    'event'            => FILTER_SANITIZE_NUMBER_INT,
    'dept_report'      => FILTER_SANITIZE_NUMBER_INT,
    'dept_name'        => FILTER_SANITIZE_SPECIAL_CHARS,
    'name'             => FILTER_SANITIZE_SPECIAL_CHARS,
    ];

    require_once(__DIR__.'/../../functions/volunteer.inc');
    require_once($FUNCTIONDIR."/database.inc");

    $updateData = filter_input_array(INPUT_GET, $arguments);

    if (isset($updateData['min_hour'])) {
        if (isset($updateData['min_hour'])) {
            $event = $updateData['event'];
        } else {
            $event = null;
        }
        print_csv_report(
            'hour_report.csv',
            volunteer_minimum_hour_report,
            $updateData['min_hour'],
            $event
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
