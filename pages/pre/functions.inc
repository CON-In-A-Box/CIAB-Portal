<?php

/*.
    require_module 'standard';
.*/

if (!empty($_REQUEST)) {
    $arguments = [
    'validate_login'   => FILTER_SANITIZE_SPECIAL_CHARS,
    'validate_passwd'  => FILTER_SANITIZE_ENCODED,
    ];

    $updateData = filter_input_array(INPUT_POST, $arguments);

    require_once($FUNCTIONDIR."/authentication.inc");

    if (isset($updateData['validate_login'])) {
        if (isset($_SESSION['IS_ADMIN']) || isset($_SESSION['IS_VOLUNTEERS'])) {
            if (!doUserAndPasswordMatch($updateData['validate_login'], urldecode($updateData['validate_passwd']))) {
                header("HTTP/1.0 401");
            }
            exit();
        }
    }
}

header("HTTP/1.0 404");
exit();
