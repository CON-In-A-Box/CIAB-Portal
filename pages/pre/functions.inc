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

    $arguments = [
    'lookupId'   => FILTER_SANITIZE_SPECIAL_CHARS,
    'useBadgeName'  => FILTER_SANITIZE_NUMBER_INT,
    ];

    $updateData = filter_input_array(INPUT_GET, $arguments);

    if (!empty($updateData['lookupId'])) {
        require_once($FUNCTIONDIR."/users.inc");

        $badge = ($updateData['useBadgeName'] == 1);
        $data = lookup_users_by_key($updateData['lookupId'], false, $badge);

        if (!$data['code']) {
            if (count($data['users']) == 0) {
                header("HTTP/1.0 404");
            }
            $user = $data['users'];
            echo json_encode($data['users']);
        } else {
            header("HTTP/1.0 ".$data['code']);
        }
        exit();
    }
}

header("HTTP/1.0 404");
exit();
