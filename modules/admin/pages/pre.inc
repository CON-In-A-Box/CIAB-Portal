<?php
if (!isset($_SESSION['IS_ADMIN'])) {
    // This is for SystemAdmins only!  Silently return to main.
    goSite();
}

require_once($FUNCTIONDIR.'/session.inc');
require_once($FUNCTIONDIR.'/database.inc');

if (!empty($_REQUEST)) {
    $arguments = [
    'SUDO'         => FILTER_SANITIZE_NUMBER_INT,
    'setField'     => FILTER_SANITIZE_SPECIAL_CHARS,
    'newField'     => FILTER_SANITIZE_SPECIAL_CHARS,
    'value'        => FILTER_UNSAFE_RAW,
    ];

    $updateData = filter_input_array(INPUT_POST, $arguments);

    if (isset($updateData['SUDO'])) {
        $_SESSION['accountId'] = $updateData['SUDO'];
        unset($_SESSION['IS_ADMIN']);
        loadAccount($updateData['SUDO']);
        goSite();
    }

    if (isset($updateData['setField'])) {
        $value = base64_decode($updateData['value']);
        set_conf_value($updateData['setField'], $value);
        exit();
    }

    if (isset($updateData['newField'])) {
        $value = base64_decode($updateData['value']);
        add_conf_value($updateData['newField'], $value);
        exit();
    }

    $arguments = [
    'downloadLog'  => FILTER_SANITIZE_SPECIAL_CHARS,
    ];

    $updateData = filter_input_array(INPUT_GET, $arguments);

    if (isset($updateData['downloadLog'])) {
        header("Content-Type: application/CSV; charset=UTF-8");
        header("Content-Language: en");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        $date = date("D M d, Y G:i");
        header('Content-Disposition: attachment; filename="DB_Activity_'.$date.'.csv"');
        header("Pragma: no-cache");
        print dump_table_to_csv('ActivityLog');
        exit();
    }
}
