<?php

/*.
    require_module 'standard';
.*/

if (!(isset($_SESSION['IS_ADMIN']) || $_SESSION['IS_VOLUNTEERS'])) {
    goSite();
}

if (!empty($_REQUEST)) {
    $arguments = [
    'toggleKiosk'      => FILTER_SANITIZE_SPECIAL_CHARS,
    'kioskUser'        => FILTER_SANITIZE_SPECIAL_CHARS,
    ];

    $updateData = filter_input_array(INPUT_POST, $arguments);
    if (isset($updateData['toggleKiosk'])) {
        if (isset($_SESSION['IS_ADMIN']) || $_SESSION['IS_VOLUNTEERS']) {
            if ($updateData['toggleKiosk'] === "true") {
                if (isset($_SESSION['kioskMode'])) {
                    unset($_SESSION['kioskMode']);
                    unset($_SESSION['kioskUser']);
                    exit();
                } else {
                    $_SESSION['kioskMode'] = 'volunteers';
                }
            }
            if (isset($updateData['kioskUser'])) {
                $_SESSION['kioskUser'] = $updateData['kioskUser'];
            }
        }
        exit();
    }
}
