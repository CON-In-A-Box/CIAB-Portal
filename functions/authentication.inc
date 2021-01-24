<?php
// Various Authentication needed routines

/*.
    require_module 'standard';
.*/

require_once(__DIR__.'/users.inc');
require_once(__DIR__.'/session.inc');
require_once(__DIR__."/../backends/email.inc");


const AUTH_SUCCESS   = 0x0;
const AUTH_BAD       = 0x1;
const AUTH_EXPIRED   = 0x2;
const AUTH_LOCKED    = 0x3;
const AUTH_DUPLICATE = 0x10;


if (array_key_exists('NEONID', $GLOBALS) &&
    array_key_exists('NEONKEY', $GLOBALS) &&
    !empty($GLOBALS['NEONID']) &&
    !empty($GLOBALS['NEONKEY'])) {
    require_once(__DIR__.'/neon.inc');
}


function LogoutUser()
{
    // Since we check for username, clearing the session logs the user out.
    // Because of that, we do not need to destroy the session, all the data and info is gone anyway.
    if (array_key_exists('Neon', $GLOBALS)) {
        $GLOBALS['Neon']->logout();
    }
    $_SESSION = [];

    // Remove cookies!
    if (isset($_SERVER['HTTP_COOKIE'])) {
        $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
        foreach ($cookies as $cookie) {
            $parts = explode('=', $cookie);
            $name = trim($parts[0]);
            if (ini_get('session.use_cookies') && $name === session_name()) {
                $p = session_get_cookie_params();
                setcookie(
                    $name,
                    '',
                    time() - 31536000,
                    $p['path'],
                    $p['domain'],
                    $p['secure'],
                    $p['httponly']
                );
            } else {
                setcookie($name, '', time() - 1000);
                setcookie($name, '', time() - 1000, '/');
            }
        }
    }

    session_destroy();
    session_write_close();

    /* restart the session */
    session_start();
    session_regenerate_id(true);

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
    if (array_key_exists('Neon', $GLOBALS)) {
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
                $sql = <<<SQL
                    UPDATE `Authentication`
                    SET OneTime = NULL,
                        OneTimeExpires = NULL
                    WHERE AccountID = $account;
SQL;
                DB::run($sql);
            }
            $now = strtotime("now");
            $expire = strtotime($value['Expires']);
            if ($now > $expire) {
                return AUTH_EXPIRED;
            }
            reset_failed($account);
            return AUTH_SUCCESS;
        }
        increment_failed($account);
        return AUTH_BAD;
    } else {
        if (array_key_exists('Neon', $GLOBALS)) {
            $value = checkNeonForUserAndPasswordMatch($user, $password);
            if ($value !== false) {
                set_password($account, $password);
                return AUTH_SUCCESS;
            }
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
            FailedAttempts = 0,
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
