<?php

if (!empty($_POST)) {
    $arguments = [
    'accountId' => FILTER_SANITIZE_NUMBER_INT,
    'accept' => FILTER_SANITIZE_SPECIAL_CHARS,
    'rooms' => FILTER_SANITIZE_SPECIAL_CHARS,
    'checkIn' => FILTER_SANITIZE_SPECIAL_CHARS,
    'checkOut' => FILTER_SANITIZE_SPECIAL_CHARS,
    'other1' => FILTER_SANITIZE_SPECIAL_CHARS,
    'other2' => FILTER_SANITIZE_SPECIAL_CHARS,
    'other3' => FILTER_SANITIZE_SPECIAL_CHARS,
    ];
    $updateData = filter_input_array(INPUT_POST, $arguments);

    $result = $Neon->pullIndividualAccount($updateData['accountId']);

    $hotelStatusFieldId = array_search('Lottery Status', $_SESSION['definedFields']);
    $roomAssignedFieldId = array_search('Room Assigned', $_SESSION['definedFields']);
    $acceptStatusFieldId = array_search('Waiting On Hotel Assignment', $_SESSION['definedFields']);
    $declinedStatusFieldId = array_search('Declined', $_SESSION['definedFields']);
    $checkInFieldId = array_search('Check In', $_SESSION['definedFields']);
    $checkOutFieldId = array_search('Check Out', $_SESSION['definedFields']);
    $other1FieldId = array_search('Other In Room 1', $_SESSION['definedFields']);
    $other2FieldId = array_search('Other In Room 2', $_SESSION['definedFields']);
    $other3FieldId = array_search('Other In Room 3', $_SESSION['definedFields']);

    $keyCheck = [ $hotelStatusFieldId, $roomAssignedFieldId, $checkInFieldId, $checkOutFieldId, $other1FieldId, $other2FieldId, $other3FieldId, ];
    
    $i = count($result['individualAccount']['customFieldDataList']['customFieldData']);
    foreach ($keyCheck as $check) {
    // Verify fieldId is in place - potential for manual add to lottery or override
        $key = array_search($check, array_column($result['individualAccount']['customFieldDataList']['customFieldData'], 'fieldId'));
        if ($key === false) {
            $result['individualAccount']['customFieldDataList']['customFieldData'][$i]['fieldId'] = $check;
            $i++;
        }
        unset($key);
    }
    unset($i);

    foreach ($result['individualAccount']['customFieldDataList']['customFieldData'] as $key => $val) {
        if (isset($val['fieldId']) && $val['fieldId'] == $hotelStatusFieldId) {
            if ($updateData['accept'] == 'Yes') {
                $result['individualAccount']['customFieldDataList']['customFieldData'][$key]['fieldOptionId'] = $acceptStatusFieldId;
            } elseif ($updateData['accept'] == 'No') {
                $result['individualAccount']['customFieldDataList']['customFieldData'][$key]['fieldOptionId'] = $declinedStatusFieldId;
            }
        } elseif (isset($val['fieldId']) && $val['fieldId'] == $roomAssignedFieldId) {
            if ($updateData['accept'] == 'Yes' && !empty($updateData['rooms'])) { // We shouldn't be passing room info to the page
                $result['individualAccount']['customFieldDataList']['customFieldData'][$key]['fieldValue'] = $updateData['rooms'];
            } elseif ($updateData['accept'] == "No" || empty($updateData['room'])) {
                unset($result['individualAccount']['customFieldDataList']['customFieldData'][$key]);
            }
        } elseif (isset($val['fieldId']) && $val['fieldId'] == $checkInFieldId) {
            if ($updateData['accept'] == 'Yes') {
                $result['individualAccount']['customFieldDataList']['customFieldData'][$key]['fieldValue'] = $updateData['checkIn'];
            } elseif ($updateData['accept'] == "No") {
                unset($result['individualAccount']['customFieldDataList']['customFieldData'][$key]);
            }
        } elseif (isset($val['fieldId']) && $val['fieldId'] == $checkOutFieldId) {
            if ($updateData['accept'] == 'Yes') {
                $result['individualAccount']['customFieldDataList']['customFieldData'][$key]['fieldValue'] = $updateData['checkOut'];
            } elseif ($updateData['accept'] == "No") {
                unset($result['individualAccount']['customFieldDataList']['customFieldData'][$key]);
            }
        } elseif (isset($val['fieldId']) && $val['fieldId'] == $other1FieldId) {
            if ($updateData['accept'] == 'Yes' && !empty($updateData['other1'])) {
                $result['individualAccount']['customFieldDataList']['customFieldData'][$key]['fieldValue'] = $updateData['other1'];
            } elseif ($updateData['accept'] == "No" || empty($updateData['room'])) {
                unset($result['individualAccount']['customFieldDataList']['customFieldData'][$key]);
            }
        } elseif (isset($val['fieldId']) && $val['fieldId'] == $other2FieldId) {
            if ($updateData['accept'] == 'Yes' && !empty($updateData['other2'])) {
                $result['individualAccount']['customFieldDataList']['customFieldData'][$key]['fieldValue'] = $updateData['other2'];
            } elseif ($updateData['accept'] == "No" || empty($updateData['room'])) {
                unset($result['individualAccount']['customFieldDataList']['customFieldData'][$key]);
            }
        } elseif (isset($val['fieldId']) && $val['fieldId'] == $other3FieldId) {
            if ($updateData['accept'] == 'Yes' && !empty($updateData['other3'])) {
                $result['individualAccount']['customFieldDataList']['customFieldData'][$key]['fieldValue'] = $updateData['other3'];
            } elseif ($updateData['accept'] == "No" || empty($updateData['room'])) {
                unset($result['individualAccount']['customFieldDataList']['customFieldData'][$key]);
            }
        }
    }

    if ($Neon->pushIndividualAccount($result)) {
    // Refresh Account info from Neon
        buildSessionData($_SESSION['accountId']);
        require_once($FUNCTIONDIR."/allocations.inc");
        if ($updateData['accept'] == 'Yes') {
            $rooms = explode('|', $updateData['rooms']);
            foreach ($rooms as $room) {
                sendChangeToHotel($room);
            }
        }
    }
}
