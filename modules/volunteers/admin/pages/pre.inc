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

require_once __DIR__.'/../../functions/volunteer.inc';

if (!empty($_REQUEST)) {
    // Retrieve and sanitize POST data
    $arguments = [
    'validate_login'   => FILTER_SANITIZE_SPECIAL_CHARS,
    'validate_passwd'  => FILTER_SANITIZE_ENCODED,
    ];

    $updateData = filter_input_array(INPUT_POST, $arguments);

    require_once($FUNCTIONDIR."/authentication.inc");

    if (isset($updateData['validate_login'])) {
        if (isset($_SESSION['IS_ADMIN']) || $_SESSION['IS_VOLUNTEERS']) {
            if (doUserAndPasswordMatch($updateData['validate_login'], urldecode($updateData['validate_passwd']))) {
                setcookie("CIAB_VOLUNTEERADMIN", true);
                exit;
            } else {
                header("HTTP/1.0 401");
                unset($_COOKIE["CIAB_VOLUNTEERADMIN"]);
                exit();
            }
        }
    }
}
