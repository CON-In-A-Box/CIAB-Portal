<?php

// Clear any previous login info
if (isset($_COOKIE[session_name()])) {
  require_once($FUNCTIONDIR . "/authentication.inc");
  LogoutUser();
}

// Check if there is a login attempt
if (!empty($_REQUEST['user'])) {
  require_once($FUNCTIONDIR . "/authentication.inc");
  if (LoginUser($_REQUEST['user'],$_REQUEST['password'])) {
    // Custom Field Data for Session
    $request = [
      'method' => 'common/listCustomFields',
      'parameters' => [
        'searchCriteria.component' => "Account",
      ],
    ];
    $result = $GLOBALS['Neon']->go($request);
    if ( isset( $result['operationResult'] ) && $result['operationResult'] == 'SUCCESS' ) {
      foreach ($result['customFields']['customField'] as $val) {
        $_SESSION['definedFields'][$val['fieldId']] = $val['fieldName'];
        if ( isset( $val['fieldOptions'] ) {
          foreach ($val['fieldOptions']['fieldOption'] as $fval) {
            $_SESSION['definedFields'][$fval['id']] = $fval['name'];
          }
          unset($fval);
        }
      }
      unset($val);
    } else {
      die("Impossible error during Defined Custom Fields Download");
    }

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
      $_SESSION['addr1'] = &$_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['addressLine1'];
      $_SESSION['addr2'] = &$_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['addressLine2'];
      $_SESSION['addr3'] = &$_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['addressLine3'];
      $_SESSION['addr4'] = &$_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['addressLine4'];
      $_SESSION['city']  = &$_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['city'];
      $_SESSION['state'] = &$_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['state']['code'];
      $_SESSION['zip']   = &$_SESSION['accountInfo']['individualAccount']['primaryContact']['addresses']['address']['0']['zipCode'];
      $_SESSION['email'] = &$_SESSION['accountInfo']['individualAccount']['primaryContact']['email1'];
      $_SESSION['phone'] = &$_SESSION['accountInfo']['individualAccount']['primaryContact']['phone1'];
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

    goSite(); // goto default
  } else {
    $RETRY=1; // Bad password or not authorized to use system
  }
}
?>
