<?php

/*.
    require_module 'standard';
.*/

if (!empty($_REQUEST)) {
    $arguments = [
    'new_token'   => FILTER_SANITIZE_SPECIAL_CHARS,
    ];
    $updateData = filter_input_array(INPUT_POST, $arguments);
    if (isset($updateData['new_token'])) {
        $_SESSION['apiAuth'] = $updateData['new_token'];
        header("HTTP/1.0 200");
        exit();
    }
}

header("HTTP/1.0 401");
exit();
