<?php

/*.
    require_module 'standard';
    require_module 'json';
.*/

require_once($FUNCTIONDIR."/database.inc");
require_once($BACKEND."/CRM.inc");
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
        \ciab\CRM::importPeople();
        exit();
    }
}
