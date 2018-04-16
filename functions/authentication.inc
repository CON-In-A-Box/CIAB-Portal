<?php
// Various Authentication needed routines

/*.
    require_module 'standard';
.*/

require_once(__DIR__.'/users.inc');
require_once(__DIR__.'/session.inc');


function LogoutUser()
{
    // Since we check for username, clearing the session logs the user out.
    // Because of that, we do not need to destroy the session, all the data and info is gone anyway.
    $GLOBALS['Neon']->logout();
    $_SESSION = [];

    // Remove cookies!
    if (isset($_SERVER['HTTP_COOKIE'])) {
        $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
        foreach ($cookies as $cookie) {
            $parts = explode('=', $cookie);
            $name = trim($parts[0]);
            setcookie($name, '', time() - 1000);
            setcookie($name, '', time() - 1000, '/');
        }
    }

}


function check_authentication($account, $password)
{
    $sql = <<<SQL
        SELECT * FROM `Authentication` WHERE AccountID = $account;
SQL;
    $result = DB::run($sql);
    $value = $result->fetch();
    if ($value !== false) {
        return password_verify($password, $value['Authentication']);
    };
    return false;

}


function doUserAndPasswordMatch($user, $password)
{
    loadDefinedFields();
    $value = lookup_user_by_login($user);
    if ($value) {
        if (check_authentication($value['AccountID'], $password)) {
            $id = $value['AccountID'];
            $now = date('Y-m-d H:i');
            $sql = <<<SQL
                UPDATE `Authentication`
                SET LastLogin = '$now'
                WHERE AccountId = $id;
SQL;
            DB::run($sql);
            return $value;
        }
    }
    return null;

}


function LoginUser($user, $password)
{
    $result = doUserAndPasswordMatch($user, $password);
    if ($result !== null) {
        // Log in the User - Session started in Functions
        $_SESSION['username']  = $user; // Save the Username for easy forms
        $_SESSION['fullName']  = $result['FirstName']; // Save the FullName for ease
        $_SESSION['accountId'] = $result['AccountID']; //Save the ID for updates
        return(true);
    } else {
        return(false);
    }

}
