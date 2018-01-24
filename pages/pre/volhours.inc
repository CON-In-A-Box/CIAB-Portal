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
    'EndDateTime'       => FILTER_SANITIZE_SPECIAL_CHARS,
    'TimeModifier'      => FILTER_VALIDATE_FLOAT,
    'DepartmentWorked'  => FILTER_SANITIZE_SPECIAL_CHARS,
    'EnteredBy'         => FILTER_SANITIZE_SPECIAL_CHARS,
    'AuthorizedBy'      => FILTER_SANITIZE_SPECIAL_CHARS,
    'volunteerId'         => FILTER_SANITIZE_SPECIAL_CHARS,
    ];
    if (!empty($_POST)) {
        $updateData = filter_input_array(INPUT_POST, $arguments);
    } else {
        $updateData = filter_input_array(INPUT_GET, $arguments);
    }

    if (!empty($updateData['VolunteerID'])
        && !empty($updateData['ActualHoursWorked'])
        && !empty($updateData['EndDateTime'])
        && !empty($updateData['TimeModifier'])
        && !empty($updateData['DepartmentWorked'])
        && !empty($updateData['EnteredBy'])
        && !empty($updateData['AuthorizedBy'])
    ) {
        $handoff = record_volunteer_hours(
            $updateData['VolunteerID'],
            $updateData['ActualHoursWorked'],
            $updateData['EndDateTime'],
            $updateData['TimeModifier'],
            $updateData['DepartmentWorked'],
            $updateData['EnteredBy'],
            $updateData['AuthorizedBy']
        );

        $updateComplete = ($handoff === null);
    } elseif (!empty($updateData['volunteerId'])) {
        include_once $FUNCTIONDIR."/users.inc";

        $data = lookup_users_by_key($updateData['volunteerId']);

        if (!$data['code']) {
            $user = $data['users'][0];
            $vol_past = get_volunteer_hours_for_user($user['Id']);
            if (!empty($vol_past)) {
                $user['volunteer'] = $vol_past;
            }
            echo json_encode($user);
        } else {
            header("HTTP/1.0 ".$data['code']);
        }

        exit();
    }
}
