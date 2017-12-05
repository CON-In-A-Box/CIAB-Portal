<?php
if (!empty($_POST)) {
    // Retrieve and sanitize POST data
    $arguments = [
    'accountId'          => FILTER_SANITIZE_NUMBER_INT,
    'contactId'          => FILTER_SANITIZE_NUMBER_INT,
    'addressId'          => FILTER_SANITIZE_NUMBER_INT,
    'firstName'          => FILTER_SANITIZE_SPECIAL_CHARS,
    'middleName'         => FILTER_SANITIZE_SPECIAL_CHARS,
    'lastName'           => FILTER_SANITIZE_SPECIAL_CHARS,
    'suffix'             => FILTER_SANITIZE_SPECIAL_CHARS,
    'preferredFirstName' => FILTER_SANITIZE_SPECIAL_CHARS,
    'preferredLastName'  => FILTER_SANITIZE_SPECIAL_CHARS,
    'email1'             => FILTER_SANITIZE_EMAIL,
    'email2'             => FILTER_SANITIZE_EMAIL,
    'email3'             => FILTER_SANITIZE_EMAIL,
    'phone1'             => FILTER_SANITIZE_SPECIAL_CHARS,
    'phone2'             => FILTER_SANITIZE_SPECIAL_CHARS,
    'conComDisplayPhone' => FILTER_SANITIZE_SPECIAL_CHARS,
    'addressLine1'       => FILTER_SANITIZE_SPECIAL_CHARS,
    'addressLine2'       => FILTER_SANITIZE_SPECIAL_CHARS,
    'city'               => FILTER_SANITIZE_SPECIAL_CHARS,
    'state'              => FILTER_SANITIZE_SPECIAL_CHARS,
    'zipCode'            => FILTER_SANITIZE_SPECIAL_CHARS,
    'zipPlus4'           => FILTER_SANITIZE_NUMBER_INT,
    'country'            => FILTER_SANITIZE_NUMBER_INT,
    'province'           => FILTER_SANITIZE_SPECIAL_CHARS,
    ];
    if (!empty($_POST)) {
        $updateData = filter_input_array(INPUT_POST, $arguments);
    } else {
        $updateData = filter_input_array(INPUT_GET, $arguments);
    }

    // Formulate the update
    $request = [
    'method' => 'account/updateIndividualAccount',
    'parameters' => [
    'individualAccount.accountId' => $updateData['accountId'],
    'individualAccount.primaryContact.contactId' => $updateData['contactId'],
    'individualAccount.primaryContact.firstName' => $updateData['firstName'],
    'individualAccount.primaryContact.middleName' => $updateData['middleName'],
    'individualAccount.primaryContact.lastName' => $updateData['lastName'],
    'individualAccount.primaryContact.suffix' => $updateData['suffix'],
    'individualAccount.primaryContact.email1' => $updateData['email1'],
    'individualAccount.primaryContact.email2' => $updateData['email2'],
    'individualAccount.primaryContact.email3' => $updateData['email3'],
    'individualAccount.primaryContact.phone1' => $updateData['phone1'],
    'individualAccount.primaryContact.phone2' => $updateData['phone2'],
    'individualAccount.primaryContact.addresses.address.addressType.Name' => 'Home',
    'individualAccount.primaryContact.addresses.address.addressId' => $updateData['addressId'],
    'individualAccount.primaryContact.addresses.address.isPrimaryAddress' => 'true',
    'individualAccount.primaryContact.addresses.address.addressLine1' => $updateData['addressLine1'],
    'individualAccount.primaryContact.addresses.address.addressLine2' => $updateData['addressLine2'],
    'individualAccount.primaryContact.addresses.address.addressLine3' => '',
    'individualAccount.primaryContact.addresses.address.addressLine4' => '',
    'individualAccount.primaryContact.addresses.address.city' => $updateData['city'],
    'individualAccount.primaryContact.addresses.address.state.code' => $updateData['state'],
    'individualAccount.primaryContact.addresses.address.zipCode' => $updateData['zipCode'],
    'individualAccount.primaryContact.addresses.address.zipCodeSuffix' => $updateData['zipPlus4'],
    'individualAccount.primaryContact.addresses.address.country.id' => $updateData['country'],
    'individualAccount.primaryContact.addresses.address.province' => $updateData['province'],
    ],
    ];

    $preferredFirstName = search_definedFields('Preferred First Name');
    $preferredLastName = search_definedFields('Preferred Last Name');
    $cvgConComPhoneDisplay = search_definedFields('CVGConCom Publish Phone');

    $i = count($_SESSION['accountInfo']['individualAccount']['customFieldDataList']['customFieldData']);
    // Verify fieldId is in place - potential for manual add to lottery or override
    $key = array_search($preferredFirstName, array_column($_SESSION['accountInfo']['individualAccount']['customFieldDataList']['customFieldData'], 'fieldId'));
    if ($key === false && !empty($updateData['preferredFirstName'])) {
        $_SESSION['accountInfo']['individualAccount']['customFieldDataList']['customFieldData'][$i]['fieldId'] = $preferredFirstName;
        $_SESSION['accountInfo']['individualAccount']['customFieldDataList']['customFieldData'][$i]['fieldValue'] = $updateData['preferredFirstName'];
        $i++;
    }
    unset($key);
    $key = array_search($preferredLastName, array_column($_SESSION['accountInfo']['individualAccount']['customFieldDataList']['customFieldData'], 'fieldId'));
    if ($key === false && !empty($updateData['preferredLastName'])) {
        $_SESSION['accountInfo']['individualAccount']['customFieldDataList']['customFieldData'][$i]['fieldId'] = $preferredLastName;
        $_SESSION['accountInfo']['individualAccount']['customFieldDataList']['customFieldData'][$i]['fieldValue'] = $updateData['preferredLastName'];
        $i++;
    }
    $key = array_search($cvgConComPhoneDisplay, array_column($_SESSION['accountInfo']['individualAccount']['customFieldDataList']['customFieldData'], 'fieldId'));
    if ($key === false && !empty($updateData['conComDisplayPhone'])) {
        $_SESSION['accountInfo']['individualAccount']['customFieldDataList']['customFieldData'][$i]['fieldId'] = $cvgConComPhoneDisplay;
        $_SESSION['accountInfo']['individualAccount']['customFieldDataList']['customFieldData'][$i]['fieldValue'] = $updateData['conComDisplayPhone'];
        $i++;
    }
    unset($key);
    unset($i);
    
    // add custom field data, watch for updated fields, if empty, drop them to clear them from Neon
    foreach ($_SESSION['accountInfo']['individualAccount']['customFieldDataList']['customFieldData'] as $key => $val) {
        if ($val['fieldId'] == $preferredFirstName) {
            if (!empty($updateData['preferredFirstName'])) {
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldId['.$key.']'] = $val['fieldId'];
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId['.$key.']'] = '';
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldValue['.$key.']'] = $updateData['preferredFirstName'];
            }
        } elseif ($val['fieldId'] == $preferredLastName) {
            if (!empty($updateData['preferredLastName'])) {
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldId['.$key.']'] = $val['fieldId'];
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId['.$key.']'] = '';
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldValue['.$key.']'] = $updateData['preferredLastName'];
            }
        } elseif ($val['fieldId'] == $cvgConComPhoneDisplay) {
            if (!empty($updateData['conComDisplayPhone'])) {
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldId['.$key.']'] = $val['fieldId'];
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId['.$key.']'] = '';
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldValue['.$key.']'] = $updateData['conComDisplayPhone'];
            }
        } else {
            $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldId['.$key.']'] = $val['fieldId'];
            if (isset($val['fieldValue'])) {
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId['.$key.']'] = '';
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldValue['.$key.']'] = $val['fieldValue'];
            } elseif (isset($val['fieldOptionId'])) {
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId['.$key.']'] = $val['fieldOptionId'];
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldValue['.$key.']'] = '';
            }
        }
    }
    unset($val);
    unset($key);

    $result = $GLOBALS['Neon']->go1($request);
    
    if (isset($result['operationResult']) && $result['operationResult'] == 'SUCCESS') {
        require_once($FUNCTIONDIR."/session.inc");
        // Refresh Account info from Neon
        buildSessionData($_SESSION['accountId']);
    } else {
        sendError("Profile Update Failed", $result);
    }
}
