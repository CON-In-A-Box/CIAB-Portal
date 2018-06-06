<?php

require_once($FUNCTIONDIR."/database.inc");
require_once($BASEDIR."/tools/neon_event_lib.inc");

/* This should only be accessable from registration staff */
if (class_exists('\\concom\\REGISTRATION') && method_exists('\\concom\\REGISTRATION', 'inRegistration')) {
    $in_registration = concom\REGISTRATION::inRegistration($_SESSION['accountId']);
} else {
    $in_registration = false;
}

if (!isset($_SESSION['IS_ADMIN']) && !$in_registration) {
    goSite();
}

if (!empty($_POST)) {
    $arguments = [
    'refreshData'  => FILTER_SANITIZE_SPECIAL_CHARS,
    ];

    $updateData = filter_input_array(INPUT_POST, $arguments);
    if (isset($updateData['refreshData'])) {
        $accountId = $updateData['refreshData'];
        $sql = <<<SQL
            SELECT
                *
            FROM
                `TempEventPage`
            WHERE
                `AccountID` = $accountId;
SQL;
        $result = DB::run($sql);
        $value = $result->fetch();
        $fields = _loadEventCustomFields();
        while ($value !== false) {
            $page = $value['PageFound'];
            $event = current_eventID();
            $people = _lookup_events_attendees($fields, $event, $page, null, false);
            $neonEvent = ['MySQLEventID' => current_eventID()];
            _import_page_of_people($neonEvent, $people, $page);
            $value = $result->fetch();
        }
        exit();
    }
}
