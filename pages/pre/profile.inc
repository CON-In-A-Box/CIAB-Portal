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
      'addressLine1'       => FILTER_SANITIZE_SPECIAL_CHARS,
      'addressLine2'       => FILTER_SANITIZE_SPECIAL_CHARS,
      'city'               => FILTER_SANITIZE_SPECIAL_CHARS,
      'state'              => FILTER_SANITIZE_SPECIAL_CHARS,
      'zipCode'            => FILTER_SANITIZE_SPECIAL_CHARS,
      'zipPlus4'           => FILTER_SANITIZE_NUMBER_INT,
      'country'            => FILTER_SANITIZE_NUMBER_INT,
      'province'           => FILTER_SANITIZE_SPECIAL_CHARS,
    ];
    $updateData = filter_input_array( INPUT_POST, $arguments );

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
    
    foreach ($_SESSION['accountInfo']['individualAccount']['customFieldDataList']['customFieldData'] as $key => $val) {
      $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldId[' . $key . ']'] = $val['fieldId'];
      if ( isset( $val['fieldValue'] )) {
        $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId[' . $key . ']'] = '';
        $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldValue[' . $key . ']'] = $val['fieldValue'];
      } elseif ( isset( $val['fieldOptionId'] )) {
        $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId[' . $key . ']'] = $val['fieldOptionId'];
        $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldValue[' . $key . ']'] = '';
      }
    }
    unset($val);
    unset($key);

    // Update the Preferred Names
    $preferredFirstName = array_search('Preferred First Name', $_SESSION['definedFields']);
    if (!empty($updateData['preferredFirstName'])) {
      $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldId[' . $preferredFirstName . ']'] = $preferredFirstName;
      $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId[' . $preferredFirstName . ']'] = '';
      $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldValue[' . $preferredFirstName . ']'] = $updateData['preferredFirstName'];
    } else {
      unset($request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldId[' . $preferredFirstName . ']']);
      unset($request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId[' . $preferredFirstName . ']']);
      unset($request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldValue[' . $preferredFirstName . ']']);
    }
    $preferredLastName = array_search('Preferred Last Name', $_SESSION['definedFields']);
    if (!empty($updateData['preferredLastName'])) {
      $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldId[' . $preferredLastName . ']'] = $preferredLastName;
      $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId[' . $preferredLastName . ']'] = '';
      $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldValue[' . $preferredLastName . ']'] = $updateData['preferredLastName'];
    } else {
      unset($request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldId[' . $preferredLastName . ']']);
      unset($request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId[' . $preferredLastName . ']']);
      unset($request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldValue[' . $preferredLastName . ']']);
    }

    $result = $GLOBALS['Neon']->go1($request);
    
    if ( isset( $result['operationResult'] ) && $result['operationResult'] == 'SUCCESS' ) {
      // Refresh Account info from Neon to verify update
      $request = [
        'method' => 'account/retrieveIndividualAccount',
        'parameters' => [
          'accountId' => $_SESSION['accountId'],
        ],
      ];
      $result = $GLOBALS['Neon']->go($request);
      if ( isset( $result['operationResult'] ) && $result['operationResult'] == 'SUCCESS' ) {
        $_SESSION['accountInfo'] = $result;
        $_SESSION['contactId'] = &$_SESSION['accountInfo']['individualAccount']['primaryContact']['contactId'];
        $_SESSION['firstName'] = &$_SESSION['accountInfo']['individualAccount']['primaryContact']['firstName'];
        $_SESSION['lastName'] = &$_SESSION['accountInfo']['individualAccount']['primaryContact']['lastName'];
        $_SESSION['addr1'] = &$_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['addressLine1'];
        $_SESSION['addr2'] = &$_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['addressLine2'];
        $_SESSION['city']  = &$_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['city'];
        $_SESSION['state'] = &$_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['state']['code'];
        $_SESSION['zip']   = &$_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['zipCode'];
        $_SESSION['email'] = &$_SESSION['accountInfo']['individualAccount']['primaryContact']['email1'];
        $_SESSION['phone'] = &$_SESSION['accountInfo']['individualAccount']['primaryContact']['phone1'];

        // Parse the custom data so we can use it easily
        foreach ($result['individualAccount']['customFieldDataList']['customFieldData'] as $val) {
          if ( isset( $val['fieldValue'] )) {
            $_SESSION['customFields'][$val['fieldId']][] = $val['fieldValue'];
          } elseif ( isset( $val['fieldOptionId'] )) {
            $_SESSION['customFields'][$val['fieldId']][] = $val['fieldOptionId'];
          } else {
            die("Impossible error during Account Custom fields formatting");
          }
        }
        unset($val);
      } else {
        die("Impossible error during Account Lookup");
      }

      // If we got here, then we are golden.  Map the custom fields for easy use

      // Support Preferred name
      if (isset($_SESSION['customFields'][$preferredFirstName])) {
        $_SESSION['preferredFirstName'] = &$_SESSION['customFields'][$preferredFirstName][0];
        $_SESSION['preferredName'] = $_SESSION['preferredFirstName'];
      } else {
        $_SESSION['preferredFirstName'] = &$_SESSION['firstName'];
        $_SESSION['preferredName'] = $_SESSION['firstName'];
      }
      $_SESSION['preferredName'] .= " ";
      if (isset($_SESSION['customFields'][$preferredLastName])) {
        $_SESSION['preferredLastName'] = &$_SESSION['customFields'][$preferredLastName][0];
        $_SESSION['preferredName'] .= $_SESSION['preferredLastName'];
      } else {
        $_SESSION['preferredLastName'] = &$_SESSION['lastName'];
        $_SESSION['preferredName'] .= $_SESSION['lastName'];
      }
    } else {
      echo "<pre>\n";
      print_r($_SESSION['accountInfo']);
      
      echo '=======';
      
      print_r($result);
      
      echo '=======';
      
      print_r($_POST);
      echo '=======';
      
      print_r($request);

      echo "</pre>\n";
      
    }
  }
?>
