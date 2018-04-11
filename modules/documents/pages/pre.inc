<?php

require_once(__DIR__.'/../functions/documents.inc');

if (!empty($_REQUEST)) {
    $arguments = [
    'setCode'    => FILTER_SANITIZE_SPECIAL_CHARS,
    'setSecret'  => FILTER_SANITIZE_SPECIAL_CHARS,
    'setFolder'  => FILTER_SANITIZE_SPECIAL_CHARS,
    ];

    $updateData = filter_input_array(INPUT_POST, $arguments);

    if (isset($updateData['setCode'])) {
        $client_result = create_client();
        verify_auth_url($client_result[0], $updateData['setCode']);
        exit();
    }

    if (isset($updateData['setSecret'])) {
        $value = $updateData['setSecret'];
        if (array_key_exists('G_CLIENT_SECRET', $GLOBALS)) {
            set_conf_value('G_CLIENT_SECRET', $value);
        } else {
            add_conf_value('G_CLIENT_SECRET', $value);
        }
        exit();
    }

    if (isset($updateData['setFolder'])) {
        $value = $updateData['setFolder'];
        $result = find_folder($value);
        if ($result !== null) {
            if (array_key_exists('G_ROOTFOLDER', $GLOBALS)) {
                set_conf_value('G_ROOTFOLDER', $result);
            } else {
                add_conf_value('G_ROOTFOLDER', $result);
            }
        }
        exit();
    }

    $arguments = [
    'loadFiles'  => FILTER_SANITIZE_SPECIAL_CHARS,
    'download'   => FILTER_SANITIZE_SPECIAL_CHARS,
    'filename'   => FILTER_SANITIZE_SPECIAL_CHARS,
    'mime'       => FILTER_SANITIZE_SPECIAL_CHARS,
    ];

    $updateData = filter_input_array(INPUT_GET, $arguments);

    if (isset($updateData['loadFiles'])) {
        $client_info = create_client();
        $service = get_drive_service($client_info[0]);
        $files = list_files($service);
        echo json_encode($files);
        exit();
    }

    if (isset($updateData['download'])) {
        $fileId = $updateData['download'];
        $filename = $updateData['filename'];
        $mime = $updateData['mime'];
        $client_info = create_client();
        $service = get_drive_service($client_info[0]);
        if (strpos($mime, 'google') === false) {
            $response = $service->files->get($fileId, array(
                'alt' => 'media'));
        } else {
            $response = $service->files->export($fileId, 'application/pdf', array(
                'alt' => 'media'));
            $filename = $filename.'.pdf';
        }
        $content = $response->getBody()->getContents();

        header("Content-Type: ".$mime."; charset=UTF-8");
        header("Content-Language: en");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header("Pragma: no-cache");
        print $content;
        exit();
    }
}
