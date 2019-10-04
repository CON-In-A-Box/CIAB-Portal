<?php
// Various Authentication needed routines

/*.
    require_module 'standard';
.*/

require_once(__DIR__.'/users.inc');
require_once(__DIR__.'/session.inc');
require_once(__DIR__."/../backends/email.inc");

if (isset($GLOBALS['NEONID']) && isset($GLOBALS['NEONKEY'])) {
    require_once($FUNCTIONDIR.'/neon.inc');
}


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


function increment_failed($account)
{
    $sql = <<<SQL
        UPDATE `Authentication`
        SET FailedAttempts = FailedAttempts + 1
        WHERE AccountID = $account;
SQL;
    $result = DB::run($sql);

}


function reset_failed($account)
{
    $sql = <<<SQL
        UPDATE `Authentication`
        SET FailedAttempts = 0
        WHERE AccountID = $account;
SQL;
    $result = DB::run($sql);

}


function checkNeonForUserAndPasswordMatch($user, $password)
{
    if (isset($GLOBALS['Neon'])) {
        $request = [
        'method' => 'common/authenticateUser',
        'parameters' => [
        'username' => $user,
        'password' => $password,
            ],
        ];
        $result = $GLOBALS['Neon']->go($request);
        if ($result['operationResult'] == 'SUCCESS') {
            $result['FirstName'] = $result['name'];
            $result['AccountID'] = $result['accountId'];
            return $result;
        }
    }
    return false;

}


const AUTH_SUCCESS   = 0x0;
const AUTH_BAD       = 0x1;
const AUTH_EXPIRED   = 0x2;
const AUTH_LOCKED    = 0x3;
const AUTH_DUPLICATE = 0x10;


function check_authentication($account, $password, $user)
{
    global $MAXLOGINFAIL;

    $max_fail = 5;

    if (isset($MAXLOGINFAIL) && !empty($MAXLOGINFAIL)) {
        $max_fail = intval($MAXLOGINFAIL);
    }

    $sql = <<<SQL
        SELECT * FROM `Authentication` WHERE AccountID = $account;
SQL;
    $result = DB::run($sql);
    $value = $result->fetch();
    if ($value !== false) {
        if ($value['FailedAttempts'] >= $max_fail) {
            return AUTH_LOCKED;
        }
        if (password_verify($password, $value['Authentication'])) {
            if ($value['OneTime']) {
                clear_onetime($account);
            }
            $now = strtotime("now");
            $expire = strtotime($value['Expires']);
            if ($now > $expire) {
                return AUTH_EXPIRED;
            }
            reset_failed($account);
            return AUTH_SUCCESS;
        }
        if ($value['OneTime']) {
            $expire = strtotime($value['OneTimeExpires']);
            $now = strtotime("now");
            if ($now > $expire) {
                clear_onetime($account);
                return AUTH_BAD;
            }
            if (password_verify($password, $value['OneTime'])) {
                accept_onetime($account, $value['OneTime']);
                reset_failed($account);
                return AUTH_SUCESS;
            }
        }
        increment_failed($account);
        return AUTH_BAD;
    } else {
        $value = checkNeonForUserAndPasswordMatch($user, $password);
        if ($value !== false) {
            set_password($account, $password);
            return AUTH_SUCCESS;
        }
        return AUTH_EXPIRED;
    }

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


function doUserAndPasswordMatch($user, $password)
{
    loadDefinedFields();
    $results = array();
    $value = lookup_user_by_login($user);
    if (count($value) > 0) {
        foreach ($value as $entry) {
            $check = check_authentication($entry['AccountID'], $password, $user);
            if ($check === AUTH_SUCCESS) {
                $id = $value[0]['AccountID'];
                $now = date('Y-m-d H:i');
                $sql = <<<SQL
                    UPDATE `Authentication`
                    SET LastLogin = '$now'
                    WHERE AccountId = $id;
SQL;
                DB::run($sql);
                if (count($value) > 1) {
                    $_SESSION['duplicates'] = array_column($value, 'AccountID');
                }
                return array("entry" => $entry, "result" => AUTH_SUCCESS);
            } else {
                array_push($results, array("entry" => null, "result" => $check));
            }
        }
    } else {
        return array("entry" => null, "result" => AUTH_BAD);
    }
    if (count($results) <= 1) {
        return $results[0];
    }
    return array("entry" => null, "result" => AUTH_DUPLICATE);

}


function LoginUser($user, $password)
{
    $result = doUserAndPasswordMatch($user, $password);
    if ($result['entry'] !== null) {
        $entry = $result['entry'];
        // Log in the User - Session started in Functions
        $_SESSION['username']  = $user; // Save the Username for easy forms
        $_SESSION['fullName']  = $entry['FirstName']; // Save the FullName for ease
        $_SESSION['accountId'] = $entry['AccountID']; //Save the ID for updates
    }
    return ($result["result"]);

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


function reset_password($id, $password = null)
{
    global $CONSITENAME, $ADMINEMAIL, $PASSWORDRESET;

    if (isset($PASSWORDRESET) && !empty($PASSWORDRESET)) {
        $duration = $PASSWORDRESET;
    } else {
        $duration = '+60 minutes';
    }

    loadDefinedFields();
    $value = lookup_user_by_login($id);
    if (count($value)) {
        $user = $value[0]['AccountID'];
        $email = $value[0]['Email'];
        if (!$password) {
            $password = rppg();
        }
        $oneexpired = date('Y-m-d H:i', strtotime($duration));
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
                LastLogin = $last,
                Expires = $realexpires,
                FailedAttempts = 0;
SQL;
        DB::run($sql);
        $servername = 'https://'.$_SERVER['SERVER_NAME'];
        $subject = 'Password Reset Request';
        $expire = date('h:i A D F jS Y', strtotime($duration));
        $adminMessage = "A password reset has been requested for '$id' on the '$CONSITENAME' web site.\n";
        $adminMessage .= "The new password is '$password' \n";
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

        \ciab\Email::mail($email, $subject, $message, $headers);
        \ciab\Email::mail($ADMINEMAIL, $subject, $adminMessage, $headers);
        error_log($adminMessage);
        return true;
    }
    return false;

}
