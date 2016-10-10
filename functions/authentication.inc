<?php
// Various Authentication needed routines - Relies on NeonCRM

function LogoutUser() {
  $_SESSION=[];
  if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
  }
  session_destroy();
}

function doUserAndPasswordMatch($user, $password) {
  // This only returns that a user is in the NeonCRM DB and that the password was matched
  if (!empty($GLOBALS['ADMINUSER']) && $user == $GLOBALS['ADMINUSER']) {
    if (crypt($password, $GLOBALS['ADMINPASS']) == $GLOBALS['ADMINPASS']) {
      // Admin user, based on local authentication, is always authorized
      $result = [
        'name' => "Admin User",
        'accountID' => -1,
      ];
      return $result;
    }
  } else {
    // Check user and password against NeonCRM
    $request = [
      'method' => 'common/authenticateUser', 
      'parameters' => [
        'username' => $user,
        'password' => $password,
      ],
    ];
    $result = $neon->go($request);

    if ($result['operationResult'] == 'SUCCESS') {
      return $result;
    } else {
      return FALSE;
    }
  }
}

function CheckInteractiveStatus($id) {
  // Is user allowed to access this Hotel system
  if($id == -1) {
    // Admin user is always allowed access
    return(TRUE);
  } else {
    // Need to look up the access field we want from NeonCRM
    $request = array( 
      'method' => 'common/listCustomFields', 
      'parameters' => array(
        'searchCriteria.component' => 'Account',
        'searchCriteria.search.name' => 'CVG Hotel System Access',
        ),
      );
    $result = $neon->go($request);
    
    // Pick up Field ID and Option ID for "Full Access"
    $AccessFieldID = $result['customFields']['customField']['fieldId'];
    $AccessOptionID = $result['customFields']['customField']['fieldOptions']['fieldOption']['id']; //##==## Should veryify name of option "Full Access"
    // Pull the user info to verify this system access
    $search = array( 
      'method' => 'account/listAccounts', 
      'criteria' => array(
        array( 'accountID', 'EQUAL', $id),
      ),
      'columns' => array(
        'standardFields' => array('Account Id', 'First Name', 'Last Name'),
        'customFields' => array($AccessFieldID),
      ),
      'page' => array(
        'currentPage' => 1,
        'pageSize' => 20,
        'sortColumn' => 'Account Id',
        'sortDirection' => 'ASC',
      ),
    );
    $result = $neon->search($search);

    if ($result) { //##==## Should Result verify the request and then we parse the result with yes or no?
      return ($result[0]); //##==## Need results
    } else {
      return(FALSE);
    }
  }
}

function LoginUser($user, $password) {
  $result = doUserAndPasswordMatch($user, $password);
  if ($result) {
    if (CheckInteractiveStatus($result['accountId'])) {
      // Log in the User - Session started in Functions
      $_SESSION['username'] = $user;
      $_SESSION['FullName'] = $result['name'];
      MakeLog("Login Allowed");
      return(TRUE);
    } else {
      MakeLog($user . " not authorized for interaction session on this system, login denied");
      LogoutUser();
      return(FALSE);
    }
  } else {
    MakeLog("Failed Login Attempt by " . $user);
    return(FALSE);
  }
}
?>
