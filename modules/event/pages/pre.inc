<?php

/*.
    require_module 'standard';
    require_module 'json';
.*/

if (!isset($_SESSION['IS_ADMIN'])) {
    // This is for SystemAdmins only!  Silently return to main.
    goSite();
}

require_once(__DIR__.'/../functions/functions.inc');

if (!empty($_REQUEST)) {
    $arguments = [
    'reloadFromNeon' => FILTER_SANITIZE_NUMBER_INT,
    ];

    $updateData = filter_input_array(INPUT_GET, $arguments);

    if (isset($updateData['reloadFromNeon'])) {
        import_events();
        import_meetings();
        header("Refresh:0; url=index.php?Function=event");
        exit();
    }

    /* POST TIME */

    $arguments = [
    'modify' => FILTER_SANITIZE_STRING,
    'delete' => FILTER_SANITIZE_NUMBER_INT,
    'cycle' => FILTER_SANITIZE_STRING,
    'deleteBadge' => FILTER_SANITIZE_NUMBER_INT,
    'badge' => FILTER_SANITIZE_STRING,
    'deleteEvent' => FILTER_SANITIZE_NUMBER_INT,
    'event' => FILTER_SANITIZE_STRING,
    'duplicateConcom' => FILTER_SANITIZE_NUMBER_INT,
    'from' => FILTER_SANITIZE_NUMBER_INT,
    ];

    $updateData = filter_input_array(INPUT_POST, $arguments);

    if (isset($updateData['modify'])) {
        $data = json_decode(base64_decode($updateData['modify']));
        save_meeting($data);
        exit();
    }

    if (isset($updateData['delete'])) {
        delete_meeting($updateData['delete']);
        exit();
    }

    if (isset($updateData['cycle'])) {
        $data = json_decode(base64_decode($updateData['cycle']));
        new_cycle($data);
        exit();
    }

    if (isset($updateData['deleteBadge'])) {
        delete_badge($updateData['deleteBadge']);
        exit();
    }

    if (isset($updateData['badge'])) {
        $data = json_decode(base64_decode($updateData['badge']));
        save_badge($data);
        exit();
    }

    if (isset($updateData['deleteEvent'])) {
        delete_event($updateData['deleteEvent']);
        exit();
    }

    if (isset($updateData['event'])) {
        $data = json_decode(base64_decode($updateData['event']));
        save_event($data);
        exit();
    }

    if (isset($updateData['duplicateConcom'])) {
        duplicate_concom($updateData['duplicateConcom'], $updateData['from']);
        exit();
    }
}
