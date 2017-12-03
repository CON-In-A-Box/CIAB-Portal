<?php
if (empty($_SESSION['customFields']['currConComPos'])) {
    // This is for ConCom members only!  Silently return to main.
    goSite();
}

require_once $FUNCTIONDIR."/volunteer.inc";

if (!empty($_REQUEST)) {
    // Retrieve and sanitize POST data
    $arguments = [
    'VolunteerID_c'       => FILTER_SANITIZE_NUMBER_INT,
    'ActualHoursWorked_c' => FILTER_VALIDATE_FLOAT,
    'EndDateTime_c'       => FILTER_SANITIZE_SPECIAL_CHARS,
    'TimeModifier_c'      => FILTER_VALIDATE_FLOAT,
    'DepartmentWorked_c'  => FILTER_SANITIZE_SPECIAL_CHARS,
    'EnteredBy_c'         => FILTER_SANITIZE_SPECIAL_CHARS,
    'AuthorizedBy_c'      => FILTER_SANITIZE_SPECIAL_CHARS,
    'volunteerId'         => FILTER_SANITIZE_SPECIAL_CHARS,
    ];
    if (!empty($_POST)) {
        $updateData = filter_input_array(INPUT_POST, $arguments);
    } else {
        $updateData = filter_input_array(INPUT_GET, $arguments);
    }

    if (!empty($updateData['VolunteerID_c'])
        && !empty($updateData['ActualHoursWorked_c'])
        && !empty($updateData['EndDateTime_c'])
        && !empty($updateData['TimeModifier_c'])
        && !empty($updateData['DepartmentWorked_c'])
        && !empty($updateData['EnteredBy_c'])
        && !empty($updateData['AuthorizedBy_c'])
    ) {
        $handoff = record_volunteer_hours(
            $updateData['VolunteerID_c'],
            $updateData['ActualHoursWorked_c'],
            $updateData['EndDateTime_c'],
            $updateData['TimeModifier_c'],
            $updateData['DepartmentWorked_c'],
            $updateData['EnteredBy_c'],
            $updateData['AuthorizedBy_c']
        );

        $updateComplete = ($handoff === null);
    } elseif (!empty($updateData['volunteerId'])) {
        include_once $FUNCTIONDIR."/users.inc";

        $user = lookup_users_by_key($updateData['volunteerId']);

        if (!empty($user)) {
            $vol_past = get_volunteer_hours_for_user($user[0]['Id']);
            if (!empty($vol_past)) {
                $user[0]['volunteer'] = $vol_past;
            }
            echo json_encode($user[0]);
        } else {
            header("HTTP/1.0 404 Not Found");
        }

        exit();
    }
}
