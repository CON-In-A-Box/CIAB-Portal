<?php

/*.
    require_module 'standard';
.*/
require_once __DIR__.'/kiosk.inc';

if (!empty($_REQUEST)) {
    $arguments = [
    'toggleKiosk'      => FILTER_SANITIZE_SPECIAL_CHARS,
    'kioskUser'        => FILTER_SANITIZE_SPECIAL_CHARS,
    ];

    $updateData = filter_input_array(INPUT_POST, $arguments);
    if (isset($updateData['toggleKiosk'])) {
        if (isset($_SESSION['IS_ADMIN']) || $_SESSION['IS_VOLUNTEERS']) {
            if ($updateData['toggleKiosk'] === "true") {
                if (in_kiosk()) {
                    leave_kiosk();
                    exit();
                } else {
                    enter_kiosk($kiosk_type);
                }
            }
            if (isset($updateData['kioskUser'])) {
                set_kiosk_user($updateData['kioskUser']);
            }
        }
        exit();
    }
}
