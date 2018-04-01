<?php
if (empty($_SESSION['customFields']['currConComPos'])) {
    // This is for ConCom members only!  Silently return to main.
    goSite();
}

require_once $FUNCTIONDIR."/volunteer.inc";

if (!empty($_REQUEST)) {
    // Retrieve and sanitize POST data
    $arguments = [
    'VolunteerID'       => FILTER_SANITIZE_NUMBER_INT,
    'ActualHoursWorked' => FILTER_VALIDATE_FLOAT,
    'EndDate'           => FILTER_SANITIZE_SPECIAL_CHARS,
    'EndHours'          => FILTER_SANITIZE_SPECIAL_CHARS,
    'EndMinutes'        => FILTER_SANITIZE_SPECIAL_CHARS,
    'EndAMPM'           => FILTER_SANITIZE_SPECIAL_CHARS,
    'TimeModifier'      => FILTER_VALIDATE_FLOAT,
    'DepartmentWorked'  => FILTER_SANITIZE_SPECIAL_CHARS,
    'EnteredBy'         => FILTER_SANITIZE_SPECIAL_CHARS,
    'AuthorizedBy'      => FILTER_SANITIZE_SPECIAL_CHARS,
    'volunteerId'       => FILTER_SANITIZE_SPECIAL_CHARS,
    'useBadgeName'      => FILTER_SANITIZE_NUMBER_INT,
    ];
    if (!empty($_POST)) {
        $updateData = filter_input_array(INPUT_POST, $arguments);
    } else {
        $updateData = filter_input_array(INPUT_GET, $arguments);
    }

    if (!empty($updateData['VolunteerID'])
        && !empty($updateData['ActualHoursWorked'])
        && !empty($updateData['EndDate'])
        && !empty($updateData['TimeModifier'])
        && !empty($updateData['DepartmentWorked'])
        && !empty($updateData['EnteredBy'])
        && !empty($updateData['AuthorizedBy'])
    ) {
        $time = intval($updateData['EndHours']);
        if ($updateData['EndAMPM'] == 'PM') {
            $time += 12;
        }
        $date = $updateData['EndDate'].'T'.$time.':';
        $date .= $updateData['EndMinutes'].":00 ";

        $handoff = record_volunteer_hours(
            $updateData['VolunteerID'],
            $updateData['ActualHoursWorked'],
            $date,
            $updateData['TimeModifier'],
            $updateData['DepartmentWorked'],
            $updateData['EnteredBy'],
            $updateData['AuthorizedBy']
        );

        $updateComplete = ($handoff === null);
    } elseif (!empty($updateData['volunteerId'])) {
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
