<?php
/*.
      require_module 'standard';
.*/

require_once($FUNCTIONDIR."/authentication.inc");

if (!empty($_POST)) {
    $arguments = [
    'password_reset' => FILTER_SANITIZE_SPECIAL_CHARS,
    ];

    $updateData = filter_input_array(INPUT_POST, $arguments);

    if (isset($updateData['password_reset'])) {
        if (!reset_password($updateData['password_reset'])) {
            header('HTTP/1.0 404 Not Found');
            echo "HTTP/1.0 404 Not Found";
        }
        exit();
    }
}
