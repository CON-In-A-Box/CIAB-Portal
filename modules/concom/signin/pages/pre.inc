<?php
/*.
      require_module 'standard';
.*/



// Sign in this user for today's meeting

if (!empty($_REQUEST['meeting'])) {
    $arguments = [
    'meeting'          => FILTER_SANITIZE_NUMBER_INT,
    ];

    $updateData = filter_input_array(INPUT_GET, $arguments);
    if (isset($updateData['meeting'])) {
        $aid = $_SESSION['accountId'];
        $mid = $updateData['meeting'];
        $sql = "INSERT INTO `MeetingAttendance` (AccountID, MeetingID) VALUES ($aid, $mid);";
        DB::run($sql);
    }
}

goSite();
