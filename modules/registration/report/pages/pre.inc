<?php

/*.
    require_module 'standard';
.*/

PageAllowedByRBAC('registration.reports');

require_once __DIR__.'/../../functions/events.inc';
require_once($FUNCTIONDIR."/reports.inc");

$reports = [];

$sql = 'SELECT * FROM `Events`';
$result = \DB::Run($sql);
$value = $result->fetch();
while ($value !== false) {
    $reports[$value['EventName'].': Memberships Sold'] = [
    'report_memberships_sold',
    $value['EventID'],
    null
    ];

    $reports[$value['EventName'].': Membership Sold Summary'] = [
    'report_sold_summary',
    $value['EventID'],
    null
    ];

    $reports[$value['EventName'].': Badges Picked Up Summary'] = [
    'registration_report',
    $value['EventID'],
    "AND rt.`BadgesPickedUp` > 0"
    ];

    $value = $result->fetch();
}

handle_report_request($reports);
