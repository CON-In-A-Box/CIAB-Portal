<?php

/*.
    require_module 'standard';
.*/

require_once($BACKEND."/RBAC.inc");

if (!\ciab\RBAC::havePermission("meeting.reports")) {
    goSite();
}

require_once __DIR__.'/../../functions/functions.inc';
require_once($FUNCTIONDIR."/reports.inc");

$reports = [
'Attendance Report' => 'attendance_report'
];

handle_report_request($reports);
