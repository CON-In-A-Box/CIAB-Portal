<?php
require_once($FUNCTIONDIR . "/authentication.inc");

// Clear any previous login info
LogoutUser();

// Check if there is a login attempt
if (!empty($_REQUEST['user'])) {
  if (LoginUser($_REQUEST['user'],$_REQUEST['password'])) {
  
    // Custom Field Data for Session - Parse it to an easy to use array
    $request = [
      'method' => 'common/listCustomFields',
      'parameters' => [
        'searchCriteria.component' => "Account",
      ],
    ];
    $result = $GLOBALS['Neon']->go($request);
    if ( isset( $result['operationResult'] ) && $result['operationResult'] == 'SUCCESS' ) {
      $_SESSION['definedFields']['customField'] = $result['customFields']['customField'];
      foreach ($result['customFields']['customField'] as $val) {
        $_SESSION['definedFields'][$val['fieldId']] = $val['fieldName'];
        if ( isset( $val['fieldOptions'] )) {
          foreach ($val['fieldOptions']['fieldOption'] as $fval) {
            $_SESSION['definedFields'][$fval['id']] = $fval['name'];
          }
          unset($fval);
          if ($val['fieldName'] == "CVGConCom Years Served") {
            foreach ($val['fieldOptions']['fieldOption'] as $fval) {
              $_SESSION['definedFields']['conComYears'][$fval['id']] = $fval['name'];
            }
            unset($fval);
          } elseif ($val['fieldName'] == "2017 Official Meeting Attendance") {
            foreach ($val['fieldOptions']['fieldOption'] as $fval) {
              $_SESSION['definedFields']['2017Meetings'][$fval['id']] = $fval['name'];
            }
            unset($fval);
          } elseif ($val['fieldName'] == "Lottery Status") {
            foreach ($val['fieldOptions']['fieldOption'] as $fval) {
              $_SESSION['definedFields']['Lottery Status'][$fval['id']] = $fval['name'];
            }
            unset($fval);
          } elseif ($val['fieldName'] == "Bed Preference") {
            foreach ($val['fieldOptions']['fieldOption'] as $fval) {
              $_SESSION['definedFields']['Bed Preference'][$fval['id']] = $fval['name'];
            }
            unset($fval);
          } elseif ($val['fieldName'] == "Hotel Qualifier") {
            foreach ($val['fieldOptions']['fieldOption'] as $fval) {
              $_SESSION['definedFields']['Hotel Qualifier'][$fval['id']] = $fval['name'];
            }
            unset($fval);
          }
        }
      }
      unset($val);      
    } else {
      die("Impossible error during Defined Custom Fields Download");
    }

    // Sort everything so displays come out correct always
    arsort($_SESSION['definedFields']['conComYears']);
    arsort($_SESSION['definedFields']['2017Meetings']);

    // Be ready if there is a preferred name need in the next section
    $preferredFirstName = array_search('Preferred First Name', $_SESSION['definedFields']);
    $preferredLastName = array_search('Preferred Last Name', $_SESSION['definedFields']);

    // Account info about User for Session
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

    // Current ConCom Positions and ConCom Years Served
    $_SESSION['customFields']['currConComPos'] = &$_SESSION['customFields'][array_search('CVGConCom Current Position', $_SESSION['definedFields'])];

    // Current Hotel Lottery Status
    $_SESSION['customFields']['hotelLotteryStatus'] = &$_SESSION['customFields'][array_search('Lottery Status', $_SESSION['definedFields'])];
    $_SESSION['customFields']['hotelAssignedRoom'] = &$_SESSION['customFields'][array_search('Room Assigned', $_SESSION['definedFields'])];
    $_SESSION['customFields']['hotelRoomConfirmation'] = &$_SESSION['customFields'][array_search('Hotel Room Confirmation', $_SESSION['definedFields'])];
    $_SESSION['customFields']['hotelRoomCheckIn'] = &$_SESSION['customFields'][array_search('Check In', $_SESSION['definedFields'])];
    $_SESSION['customFields']['hotelRoomCheckOut'] = &$_SESSION['customFields'][array_search('Check Out', $_SESSION['definedFields'])];
    
    // If the logged in user is a System Admin (defined in the config file), note it
    if(in_array($_SESSION['accountId'], $ALLOCATIONS)) {
      $_SESSION['IS_ALLOCATIONS'] = TRUE;
    }

    // If the logged in user is a System Admin (defined in the config file), note it
    if(in_array($_SESSION['accountId'], $ADMINACCOUNTS)) {
      $_SESSION['IS_ADMIN'] = TRUE;
    }

    goSite(); // goto default
  } else {
    $RETRY=1; // Bad password or not authorized to use system
  }
}
?>
