<?php
// Various Authentication needed routines - Relies on NeonCRM

function LogoutUser()
{
    // Since we check for username, clearing the session logs the user out.  Cookie has no useful info.
    // Because of that, we do not need to destroy the session, all the data and info is gone anyway.
    $GLOBALS['Neon']->logout();
    $_SESSION = [];

}


function doUserAndPasswordMatch($user, $password)
{
    // This only returns that a user is in the NeonCRM DB and that the password was matched
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
        return false;
    }

}


function LoginUser($user, $password)
{
    $result = doUserAndPasswordMatch($user, $password);
    if ($result) {
        // Log in the User - Session started in Functions
        $_SESSION['username']  = $user; // Save the Username for easy forms
        $_SESSION['fullName']  = $result['name']; // Save the FullName for ease
        $_SESSION['accountId'] = $result['accountId']; //Save the ID for updates
        // MakeLog("Login Allowed");
        //error_log("Notice: Login allowed for " . $user . " (" . $result['accountId'] . ") as " . $result['name'] . " at " . date('r'), 0);
        return(true);
    } else {
        // MakeLog("Failed Login Attempt by " . $user);
        //error_log("WARN: Failed Login Attempt By " . $user . " at " . date('r'), 0);
        return(false);
    }

}
