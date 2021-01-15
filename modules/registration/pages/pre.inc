<?php

/*.
    require_module 'standard';
    require_module 'json';
.*/

require_once($FUNCTIONDIR."/database.inc");
require_once($BASEDIR."/tools/neon_event_lib.inc");
require_once(__DIR__."/../functions/events.inc");

/* This should only be accessable from registration staff */
$access = in_registration();

if (!isset($_SESSION['IS_ADMIN']) && !$access) {
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
