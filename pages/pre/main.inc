<?php
require_once($FUNCTIONDIR.'/users.inc');
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

    $result = pullIndividualAccount($updateData['accountId']);

    $hotelStatusFieldId = search_definedFields('Lottery Status');
    $roomAssignedFieldId = search_definedFields('Room Assigned');
    $acceptStatusFieldId = search_definedFields('Waiting On Hotel Assignment');
    $declinedStatusFieldId = search_definedFields('Declined');
    $checkInFieldId = search_definedFields('Check In');
    $checkOutFieldId = search_definedFields('Check Out');
    $other1FieldId = search_definedFields('Other In Room 1');
    $other2FieldId = search_definedFields('Other In Room 2');
    $other3FieldId = search_definedFields('Other In Room 3');

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

    if (pushIndividualAccount($result)) {
        // Refresh Account info from Neon
        require_once($FUNCTIONDIR.'/session.inc');
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
