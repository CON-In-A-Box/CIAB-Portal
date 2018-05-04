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


function clear_onetime($account)
{
    $sql = <<<SQL
        UPDATE `Authentication`
        SET OneTime = NULL,
            OneTimeExpires = NULL
        WHERE AccountID = $account;
SQL;
    DB::run($sql);

}


function check_authentication($account, $password)
{
    $sql = <<<SQL
        SELECT * FROM `Authentication` WHERE AccountID = $account;
SQL;
    $result = DB::run($sql);
    $value = $result->fetch();
    if ($value !== false) {
        if (password_verify($password, $value['Authentication'])) {
            if ($value['OneTime']) {
                clear_onetime($account);
            }
            $now = strtotime("now");
            $expire = strtotime($value['Expires']);
            if ($now > $expire) {
                return null;
            }
            return true;
        }
        if ($value['OneTime']) {
            $expire = strtotime($value['OneTimeExpires']);
            $now = strtotime("now");
            if ($now > $expire) {
                clear_onetime($account);
                return false;
            }
            if (password_verify($password, $value['OneTime'])) {
                accept_onetime($account, $value['OneTime']);
                return true;
            }
        }
        return false;
    } else {
        return null;
    }

}


function doUserAndPasswordMatch($user, $password)
{
    loadDefinedFields();
    $value = lookup_user_by_login($user);
    if ($value) {
        $check = check_authentication($value['AccountID'], $password);
        if ($check === true) {
            $id = $value['AccountID'];
            $now = date('Y-m-d H:i');
            $sql = <<<SQL
                UPDATE `Authentication`
                SET LastLogin = '$now'
                WHERE AccountId = $id;
SQL;
            DB::run($sql);
            return $value;
        } elseif ($check === null) {
            return null;
        }
    }
    return false;

}


function LoginUser($user, $password)
{
    $result = doUserAndPasswordMatch($user, $password);
    if ($result !== null && $result !== false) {
        // Log in the User - Session started in Functions
        $_SESSION['username']  = $user; // Save the Username for easy forms
        $_SESSION['fullName']  = $result['FirstName']; // Save the FullName for ease
        $_SESSION['accountId'] = $result['AccountID']; //Save the ID for updates
        return(true);
    } elseif ($result === false) {
        return(false);
    }
    return null;

}


function set_password($user, $password, $require_hash = true)
{
    global $PASSWORDEXPIRE;

    if (isset($PASSWORDEXPIRE) && !empty($PASSWORDEXPIRE)) {
        $duration = $PASSWORDEXPIRE;
    } else {
        $duration = '+1 year';
    }
    $expires = date('Y-m-d H:i', strtotime($duration));
    if ($require_hash) {
        $auth = password_hash($password, PASSWORD_DEFAULT);
    } else {
        $auth = $password;
    }

    $last = 'NULL';
    $sql = "SELECT * FROM  `Authentication` WHERE AccountID = $user;";
    $result = DB::run($sql);
    $value = $result->fetch();
    if ($value !== false && $value['LastLogin'] !== null) {
        $last = "'".$value['LastLogin']."'";
    }

    $sql = <<<SQL
        REPLACE INTO `Authentication`
        SET AccountID = $user,
            Authentication = '$auth',
            LastLogin = $last,
            Expires = '$expires',
            OneTime = NULL,
            OneTimeExpires = NULL;
SQL;
    DB::run($sql);

}


function accept_onetime($account, $auth)
{
    set_password($account, $auth, false);

}


function rppg()
{
    $pw = '';
    $c  = 'bcdfghjklmnprstvwz'; // consonants except hard to speak ones
    $v  = 'aeiou';              // vowels
    $a  = $c.$v;                // all

  //use two syllables...
    for ($i = 0; $i < 3; $i++) {
        $sy = $c[rand(0, strlen($c) - 1)];
        $sy .= $v[rand(0, strlen($v) - 1)];
        $sy .= $a[rand(0, strlen($a) - 1)];
        if (rand(0, 1) == 0) {
            $sy = ucfirst($sy);
        }
        $pw .= $sy;
    }
  //... and add a nice number
    $pw .= rand(10, 99);

    return $pw;

}


function reset_password($id)
{
    global $CONSITENAME;

    loadDefinedFields();
    $value = lookup_user_by_login($id);
    if ($value) {
        $user = $value['AccountID'];
        $email = $value['EMail'];
        $password = rppg();
        $oneexpired = date('Y-m-d H:i', strtotime("+10 minutes"));
        $newauth = password_hash($password, PASSWORD_DEFAULT);
        $auth = 'NULL';
        $last = 'NULL';
        $realexpires = 'NULL';

        $sql = "SELECT * FROM  `Authentication` WHERE AccountID = $user;";
        $result = DB::run($sql);
        $value = $result->fetch();
        if ($value !== false) {
            $auth = "'".$value['Authentication']."'";
            $last = "'".$value['LastLogin']."'";
            $realexpires = "'".$value['Expires']."'";
        }

        $sql = <<<SQL
            REPLACE INTO `Authentication`
            SET AccountID = $user,
                OneTime = '$newauth',
                OneTimeExpires= '$oneexpired',
                Authentication = $auth,
                LastLogin = $last;
                Expires = '$realexpires',
SQL;
        DB::run($sql);
        $servername = 'https://'.$_SERVER['SERVER_NAME'];
        $subject = 'Password Reset Request';
        $expire = date('h:i A D F jS Y', strtotime("+10 minutes"));
        $message = <<<MSG
A password reset has been requested for '$id' on the '$CONSITENAME' web site.

If this is correct then please use this password '$password' to log in and
change yor password to one you like.

$servername

If you did not request this please log into your account as soon as possible
to verify everything is still correct.

This new password will expire at $expire;

Thank you
MSG;
        $headers = 'From: noreply@convergence-con.org'."\r\n";
        $headers .= 'Reply-To: noreply@convergence-con.org';

        mail($email, $subject, $message, $headers);
        error_log($message);
        return true;
    }
    return false;

}
