<?php
/*.
    require_module 'standard';
.*/

require_once($BACKEND."/RBAC.inc");

if (!\ciab\RBAC::havePermission("site.admin")) {
    // This is for SystemAdmins only!  Silently return to main.
    goSite();
}

require_once($FUNCTIONDIR.'/session.inc');
require_once($FUNCTIONDIR.'/database.inc');
require_once($FUNCTIONDIR.'/authentication.inc');


function delete_files($target)
{
    if (is_dir($target)) {
        $files = glob($target.'*', GLOB_MARK);

        foreach ($files as $file) {
            delete_files($file);
        }

        @rmdir($target);
    } elseif (is_file($target)) {
        @unlink($target);
    }

}


if (!empty($_REQUEST)) {
    $arguments = [
    'SUDO'         => FILTER_SANITIZE_NUMBER_INT,
    'setField'     => FILTER_SANITIZE_SPECIAL_CHARS,
    'newField'     => FILTER_SANITIZE_SPECIAL_CHARS,
    'value'        => FILTER_UNSAFE_RAW,
    'removeAdmin'  => FILTER_VALIDATE_BOOLEAN,
    'updateMemberships' => FILTER_VALIDATE_BOOLEAN,
    'hook_action'  => FILTER_SANITIZE_SPECIAL_CHARS,
    'hook_name'    => FILTER_SANITIZE_SPECIAL_CHARS,
    'hook_url'     => FILTER_SANITIZE_SPECIAL_CHARS,
    'rebuildSCSS'  => FILTER_VALIDATE_BOOLEAN,
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

    if (isset($updateData['removeAdmin'])) {
        unset($_SESSION['IS_ADMIN']);
        goSite();
    }

    if (isset($updateData['updateMemberships'])) {
        require($BASEDIR.'/tools/sync_neon_event_to_db.php');
    }

    if (isset($updateData['hook_action'])) {
        if ($updateData['hook_action'] == "install") {
            neon_registerWebhook(
                $updateData['hook_url'],
                $updateData['hook_name']
            );
        } else {
            neon_removeWebhook();
        }
        goSite('/index.php?Function=admin');
    }

    if (isset($updateData['rebuildSCSS'])) {
        delete_files(@sys_get_temp_dir().'/scss_cache');
        goSite();
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
        print dump_table_to_csv(
            'ActivityLog',
            false,
            ["/(Authentication = ')(.*)(')/i", "/(OneTime = ')(.*)(')/i"]
        );
        exit();
    }
}
