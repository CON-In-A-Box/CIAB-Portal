<?php

/*.
    require_module 'standard';
.*/
require_once __DIR__.'/console.inc';

if (!empty($_REQUEST)) {
    $arguments = [
    'toggleConsole'      => FILTER_SANITIZE_SPECIAL_CHARS,
    'consoleUser'        => FILTER_SANITIZE_SPECIAL_CHARS,
    ];

    $updateData = filter_input_array(INPUT_POST, $arguments);
    if (isset($updateData['toggleConsole'])) {
        if (isset($_SESSION['IS_ADMIN']) || $_SESSION['IS_VOLUNTEERS']) {
            if ($updateData['toggleConsole'] === "true") {
                if (in_console()) {
                    leave_console();
                    exit();
                } else {
                    enter_console($console_type);
                }
            }
            if (isset($updateData['consoleUser'])) {
                set_console_user($updateData['consoleUser']);
            }
        }
        exit();
    }
}
