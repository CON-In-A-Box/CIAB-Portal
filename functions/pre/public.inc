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
    // Collect User and Custom Field Data for Session
    $request = [
      'method' => 'account/retrieveIndividualAccount',
      'parameters' => [
        'accountId' => $_SESSION['accountId'],
      ],
    ];
    $result = $GLOBALS['Neon']->go($request);
    if ( isset( $result['operationResult'] ) && $result['operationResult'] == 'SUCCESS' ) {
      $_SESSION['accountInfo'] = $result;
    } else {
      die("Impossible error during Account Lookup");
    }
    
    $request = [
      'method' => 'common/listCustomFields',
      'parameters' => [
        'searchCriteria.component' => "Account",
      ],
    ];
    $result = $GLOBALS['Neon']->go($request);
    if ( isset( $result['operationResult'] ) && $result['operationResult'] == 'SUCCESS' ) {
      $_SESSION['customFields'] = $result;
    } else {
      die("Impossible error during Custom Fields Download");
    }

    goSite(); // goto default
  } else {
    $RETRY=1; // Bad password or not authorized to use system
  }
}
?>
