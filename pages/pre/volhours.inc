<?php
if (empty($_SESSION['customFields']['currConComPos'])) {
    // This is for ConCom members only!  Silently return to main.
    goSite();
}

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

    if (!empty($updateData['VolunteerID_c']) && !empty($updateData['ActualHoursWorked_c']) && !empty($updateData['EndDateTime_c']) && !empty($updateData['TimeModifier_c']) && !empty($updateData['DepartmentWorked_c']) && !empty($updateData['EnteredBy_c']) && !empty($updateData['AuthorizedBy_c'])) {
        $request = [
        'method' => 'customObjectRecord/createCustomObjectRecord',
        'parameters' => [
        'customObjectRecord.objectApiName' => 'VolHourEntries_c',
        ],
        ];

        $fields = ['VolunteerID_c', 'ActualHoursWorked_c', 'EndDateTime_c', 'TimeModifier_c', 'DepartmentWorked_c', 'EnteredBy_c', 'AuthorizedBy_c'];
        foreach ($fields as $key => $field) {
            $request['parameters']['customObjectRecord.customObjectRecordDataList.customObjectRecordData.name['.$key.']'] = $field;
            $request['parameters']['customObjectRecord.customObjectRecordDataList.customObjectRecordData.value['.$key.']'] = $updateData[$field];
        }

        unset($key);
        unset($value);

        $result = $GLOBALS['Neon']->go1($request);

        if (isset($result['operationResult']) && $result['operationResult'] == 'SUCCESS') {
            $updateComplete = true;
        } else {
            $updateComplete = false;
            sendError("Fail to record Volunteer hours ".__FILE__, array_merge($request, $result));
            $handoff = array_merge($request, $result);
        }
    } elseif (!empty($updateData['volunteerId'])) {
        require_once($FUNCTIONDIR."/users.inc");
        if (is_numeric($updateData['volunteerId'])) {
            $user = lookup_user_by_id($updateData['volunteerId']);
        } else {
            $user = lookup_users_by_email($updateData['volunteerId']);
        }

        if (!empty($user)) {
            echo json_encode($user[0]);
        } else {
            header("HTTP/1.0 404 Not Found");
        }

        exit();
    }
}
