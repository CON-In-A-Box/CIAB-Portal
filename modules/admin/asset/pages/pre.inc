<?php

/*.
    require_module 'standard';
    require_module 'json';
    require_module 'file';
.*/

PageAllowedByRBAC('asset.admin');

require_once($FUNCTIONDIR."/database.inc");
require_once($BACKEND.'/asset.inc');

if (!empty($_REQUEST)) {
    if (isset($_POST['AssetID'])) {
        if (!isset($_FILES['AssetData']['error']) ||
            is_array($_FILES['AssetData']['error'])) {
            exit();
        }

        switch ($_FILES['AssetData']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                error_log('No file sent.');
                exit();
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                error_log('Exceeded filesize limit.');
                exit();
            default:
                error_log('Unknown errors.');
                exit();
        }

        $target = sprintf(
            sys_get_temp_dir().'/%s.%s',
            sha1_file($_FILES['AssetData']['tmp_name']),
            '.img'
        );
        $path = realpath(dirname($_FILES['AssetData']['tmp_name']));
        $base = basename($_FILES['AssetData']['tmp_name']);
        if (!move_uploaded_file($path.DIRECTORY_SEPARATOR.$base, $target)) {
            error_log('File move failed.');
            exit();
        }

        $data = file_get_contents($target);
        \ciab\Asset::save($_POST['AssetID'], $data);
        exit();
    }
}

if (isset($_GET['configuration'])) {
    $config = \ciab\Asset::configuration();
    foreach ($config as $key => $value) {
        if (array_key_exists($key, $_ENV)) {
            $config[$key]['source'] = 'ENV';
        } elseif (array_key_exists($key, $GLOBALS)) {
            $config[$key]['source'] = 'DB';
        } else {
            $config[$key]['source'] = 'NEW';
        }
        if (!RBACApiRequest('site.admin')) {
            $config[$key]['readonly'] = true;
        }
    }
    print json_encode($config);
    exit();
}
