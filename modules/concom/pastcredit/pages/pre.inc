<?php
/*.
      require_module 'standard';
.*/

require_once(__DIR__."/../../functions/ATTENDANCE.inc");

// Request Past Attendance Credit from Divisional Directors

if (!empty($_REQUEST['meeting'])) {
    $arguments = [
    'meeting'          => FILTER_SANITIZE_NUMBER_INT,
    ];

    $updateData = filter_input_array(INPUT_GET, $arguments);
    if (isset($updateData['meeting'])) {
        request_past_attendance($updateData['meeting']);
    }
}

goSite();
