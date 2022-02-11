<?php
if (empty($_SESSION['customFields']['currConComPos'])) {
    // This is for ConCom members only!  Silently return to main.
    goSite();
}

require_once __DIR__.'/../../functions/volunteer.inc';

if (!empty($_REQUEST)) {
    // Retrieve and sanitize POST data
    $arguments = [
    'volunteerId'       => FILTER_SANITIZE_SPECIAL_CHARS
    ];
    if (!empty($_POST)) {
        $updateData = filter_input_array(INPUT_POST, $arguments);
    } else {
        $updateData = filter_input_array(INPUT_GET, $arguments);
    }
    if (!empty($updateData['volunteerId'])) {
        include_once $FUNCTIONDIR."/users.inc";

        $badge = ($updateData['useBadgeName'] == 1);
        $data = lookup_users_by_key($updateData['volunteerId'], false, $badge);

        if (!$data['code']) {
            if (count($data['users']) == 0) {
                header("HTTP/1.0 404");
            }
            $users = $data['users'];
            foreach ($users as $key => $data) {
                $vol_past = get_volunteer_hours_for_user($data['Id']);
                if (!empty($vol_past)) {
                    $users[$key]['volunteer'] = $vol_past;
                }
            }
            echo json_encode($users);
        } else {
            header("HTTP/1.0 ".$data['code']);
        }
        exit();
    }
}
