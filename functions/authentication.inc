<?php
// Various Authentication needed routines - Relies on NeonCRM

function LogoutUser() {
  // Since we check for username, clearing the session logs the user out.  Cookie has no useful info.
  // No need to destroy the session since it will time out anyway, and has no helpful info
  $tmp = $_SESSION['neonSession'];
  $_SESSION = [];
  $_SESSION['neonSession'] = $tmp;
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
    $result = $GLOBALS['Neon']->go($request);

    if ($result['operationResult'] == 'SUCCESS') {
      return $result;
    } else {
      return FALSE;
    }
  }
}

function LoginUser($user, $password) {
  $result = doUserAndPasswordMatch($user, $password);
  if ($result) {
    // Log in the User - Session started in Functions
    $_SESSION['username']  = $user; // Save the Username for easy forms
    $_SESSION['fullName']  = $result['name']; // Save the FullName for ease
    $_SESSION['accountId'] = $result['accountId']; //Save the ID for updates
    // MakeLog("Login Allowed");
    //error_log("Notice: Login allowed for " . $user . " (" . $result['accountId'] . ") as " . $result['name'] . " at " . date('r'), 0);
    return(TRUE);
  } else {
    // MakeLog("Failed Login Attempt by " . $user);
    //error_log("WARN: Failed Login Attempt By " . $user . " at " . date('r'), 0);
    return(FALSE);
  }
}
?>
