<?php

/*.
    require_module 'standard';
    require_module 'json';
.*/

require_once(__DIR__."/../backends/CRM.inc");

function _quote($value)
{
    if ($value === null || $value === '') {
        return 'NULL';
    } else {
        return \MyPDO::quote($value);
    }

}

function _parse_user($result, $additional_fields)
{
    $user = array();

    $user['Id'] = $result['AccountID'];
    if (!empty($result['PreferredFirstName'])) {
        $user['First Name'] = $result['PreferredFirstName'];
    } else {
        $user['First Name'] = $result['FirstName'];
    }
    if (!empty($result['PreferredLastName'])) {
        $user['Last Name'] = $result['PreferredLastName'];
    } else {
        $user['Last Name'] = $result['LastName'];
    }
    if (class_exists('\\concom\\POSITION') && method_exists('\\concom\\POSITION', 'getConComPosition')) {
        $user['ConCom'] = concom\POSITION::getConComPosition($user['Id']);
    }
    if (!empty($result['Email'])) {
        $user['Email'] = $result['Email'];
    }
    if (!empty($additional_fields)) {
        foreach ($additional_fields as $field) {
            $user[$field] = $result[$field];
        }
    }

    return $user;

}


function _lookup_user($criteria, $single_result = false, $fields = array())
{
    $sql = "SELECT * FROM `Members` WHERE $criteria;";

    $result = DB::run($sql);
    if ($single_result && $result->rowCount() > 1) {
        return array('code' => '409 Conflict', 'users' => array());
    }
    $value = $result->fetch();
    if ($value === false) {
        return array('code' => '404 Not Found', 'users' => array());
    }
    $output = ['code' => null, 'users' => array()];
    while ($value !== false) {
        $user = _parse_user($value, $fields);
        array_push($output['users'], $user);
        $value = $result->fetch();
    }
    return $output;

}


function lookup_users_by_ids($ids, $fields = array())
{
    if (\ciab\CRM::active()) {
        return \ciab\CRM::lookupUsersByIds($ids, false, $fields);
    }
    $str = implode(", ", $ids);
    return _lookup_user("AccountId IN ($str)", false, $fields);

}


function lookup_user_by_id($id, $fields = array())
{
    $id = trim($id);
    if (\ciab\CRM::active()) {
        return \ciab\CRM::lookupUsersByIds($id, true, $fields);
    }
    return _lookup_user("AccountID = $id", true, $fields);

}


function lookup_users_by_email($email, $single = true, $substring = false, $fields = array())
{
    $email = trim($email);
    if (\ciab\CRM::active()) {
        return \ciab\CRM::lookupUsersByEmail($email, $single, $substring, $fields);
    }
    if ($substring) {
        return _lookup_user("Email LIKE '%$email%'", $single, $fields);
    } else {
        return _lookup_user("Email = '$email'", $single, $fields);
    }

}


$_set = array();


function filter_users($user)
{
    global $_set;
    if (in_array($user['Id'], $_set)) {
        return false;
    } else {
        $_set[] = $user['Id'];
        return true;
    }

}


function sort_users($a, $b)
{
    return intval($a['Id']) > intval($b['Id']);

}


function merge_results($result1, $result2)
{
    global $_set;
    $_set = array();
    $output = ['code' => '404 Not Found', 'users' => array()];

    if ($result1['code'] === null) {
        $output['code'] = null;
        $output['users'] = array_merge($output['users'], $result1['users']);
        foreach ($result1['users'] as $u) {
            $_set[] = $u['Id'];
        }
    }

    if ($result2['code'] === null) {
        if ($output['code'] === null) {
            $result = array_filter($result2['users'], 'filter_users');
        } else {
            $result = $result2['users'];
            $output['code'] = null;
        }
        $output['users'] = array_merge($output['users'], $result);
    }

    usort($output['users'], "sort_users");

    return $output;

}


function lookup_users_by_name($name, $single = false, $substring = false, $fields = array())
{
    $name = trim(preg_replace('/\s+/', ' ', $name));
    if (\ciab\CRM::active()) {
        return \ciab\CRM::lookupUsersByName($name, $single, $substring, false, $fields);
    }
    $names = explode(" ", $name);
    $i2 = 1;
    if (count($names) < 2) {
        $i2 = 0;
    }
    if ($substring) {
        $query = "FirstName LIKE '%".$names[0]."%' OR LastName LIKE '%".$names[$i2]."%'";
    } elseif ($i2 > 0) {
        $query = "FirstName = '$names[0]' AND LastName = '$names[1]'";
    } else {
        return array('code' => '404 Not Found', 'users' => array());
    }

    return _lookup_user($query, $single, $fields);

}


function lookup_users_by_pref_name($name, $single = false, $substring = false, $fields = array())
{
    $name = trim(preg_replace('/\s+/', ' ', $name));
    if (\ciab\CRM::active()) {
        return \ciab\CRM::lookupUsersByName($name, $single, $substring, true, $fields);
    }
    $names = explode(" ", $name);
    $i2 = 1;
    if (count($names) < 2) {
        $i2 = 0;
    }
    if ($substring) {
        if (count($names) < 2) {
            $query = <<<Q
    (PreferredFirstName LIKE '%$name%' OR
     PreferredLastName LIKE '%$name%' OR
     LastName LIKE '%$name%' OR
    FirstName LIKE '%$name%')
Q;
        } else {
            $query = <<<Q
    (PreferredFirstName = '$names[0]' AND PreferredLastName LIKE '%$names[1]%') OR
    (PreferredFirstName = '$names[0]' AND LastName LIKE '%$names[1]%') OR
    (FirstName = '$names[0]' AND PreferredLastName LIKE '%$names[1]%')
    (PreferredFirstName LIKE '%$names[0]%' AND PreferredLastName = '$names[1]') OR
    (PreferredFirstName LIKE '%$names[0]%' AND LastName = '$names[1]') OR
    (FirstName LIKE '%$names[0]%' AND PreferredLastName = '$names[1]')
Q;
        }
    } elseif ($i2 > 0) {
        $query = <<<Q
    (PreferredFirstName = '$names[0]' AND PreferredLastName = '$names[1]') OR
    (PreferredFirstName = '$names[0]' AND LastName = '$names[1]') OR
    (FirstName = '$names[0]' AND PreferredLastName = '$names[1]')
Q;
    } else {
        return array('code' => '404 Not Found', 'users' => array());
    }
    return _lookup_user($query, $single, $fields);

}


function lookup_users_by_badgename(
    $badge,
    $single_user = false,
    $substring = false,
    $fields = array()
) {
    $badge = trim($badge);
    if ($substring) {
        $sql = <<<SQL
            SELECT
                AccountID
            FROM
                `Registrations`
            WHERE
                BadgeName LIKE '%{$badge}%'
SQL;
    } else {
        $sql = <<<SQL
            SELECT
                AccountID
            FROM
                `Registrations`
            WHERE
                BadgeName = '$badge'
SQL;
    }
    return _lookup_user("AccountID IN ($sql)", $single_user, $fields);

}


function lookup_users_by_key($key, $single = true, $badgename = true, $substring = false, $fields = array())
{
    $key = trim(preg_replace('/\s+/', ' ', $key));
    if ($substring) {
        $output = ['code' => '404 Not Found', 'users' => array()];
        if (is_numeric($key) ||
            (strtoupper($key)[0] == 'A' && is_numeric(substr($key, 1)))) {
            if (strtoupper($key)[0] == 'A') {
                $result = lookup_user_by_id(substr($key, 1), $fields);
                $output = merge_results($output, $result);
            } else {
                $result = lookup_user_by_id($key, $fields);
                $output = merge_results($output, $result);
            }
        }
        $result = lookup_users_by_email($key, $single, true, $fields);
        $output = merge_results($output, $result);
        $result = lookup_users_by_name($key, $single, true, $fields);
        $output = merge_results($output, $result);
        $result = lookup_users_by_pref_name($key, $single, true, $fields);
        $output = merge_results($output, $result);
        $result = lookup_users_by_badgename($key, $single, true, $fields);
        $output = merge_results($output, $result);
        return $output;
    } else {
        if (is_numeric($key) ||
            (strtoupper($key)[0] == 'A' && is_numeric(substr($key, 1)))) {
            if (strtoupper($key)[0] == 'A') {
                return lookup_user_by_id(substr($key, 1), $fields);
            } else {
                return lookup_user_by_id($key, $fields);
            }
        } elseif (strpos($key, '@') !== false) {
            return lookup_users_by_email($key, $single, false, $fields);
        } else {
            $output = lookup_users_by_name($key, $single, $fields);
            if (count($output['users']) == 0) {
                $output = lookup_users_by_pref_name($key, $single, $fields);
            }
            if ($badgename && count($output['users']) == 0) {
                $output = lookup_users_by_badgename($key, $single, $fields);
            }
            return $output;
        }
    }

}


function lookup_users_by_name_email($first, $last, $email, $fields, $single_user = false, $substring = false)
{
    if (\ciab\CRM::active()) {
        return \ciab\CRM::lookupUsersByNameEmail($first, $last, $email, $fields, $single_user, $substring);
    }
    $criteria = array();
    if ($first) {
        if ($substring) {
            array_push($criteria, "FirstName LIKE '%$first%'");
        } else {
            array_push($criteria, "FirstName = '$first'");
        }
    }
    if ($last) {
        if ($substring) {
            array_push($criteria, "LastName LIKE '%$last%'");
        } else {
            array_push($criteria, "LastName = '$last'");
        }
    }
    if ($email) {
        if ($substring) {
            array_push($criteria, "Email LIKE '%$email%'");
        } else {
            array_push($criteria, "Email = '$email'");
        }
    }

    $stmt = implode(" AND ", $criteria);

    return _lookup_user($stmt, $single_user, $fields);

}


function lookup_users_by_login($user, $substring = false, $fields = array())
{
    $output = lookup_users_by_email($user, false, $substring, $fields);
    if (count($output['users']) == 0) {
        if (\ciab\CRM::active()) {
            $output = \ciab\CRM::lookupUserByLogin($user, $substring, $fields);
        } else {
            if ($substring) {
                $output = _lookup_user("Login LIKE '%$user%'", false, $fields);
            } else {
                $output = _lookup_user("Login = '$user'", false, $fields);
            }
        }
    }
    if (count($output['users']) == 0) {
        return array();
    } else {
        foreach ($output['users'] as $key => $user) {
            $output['users'][$key]['AccountID'] = $user['Id'];
        }
        return $output['users'];
    }

}


function pullIndividualAccount($accountId)
{
    $sql = "SELECT * FROM `Members` WHERE AccountId = $accountId;";
    $result = \DB::run($sql);
    $value = $result->fetch();
    if ($value === false) {
        return null;
    } else {
        return $value;
    }

}


function listCountries()
{
    $datafile = file_get_contents(__DIR__.'/../data/countries.json');
    $data = json_decode($datafile);

    $countries = array();
    foreach ($data as $key => $line) {
        array_push($countries, ['id' => $key, 'name' => $line]);
    }

    return $countries;

}


function _process_field(&$data, $updateData, $field, $target, $binary = false)
{
    if (isset($updateData[$field])) {
        if ($binary || !empty($updateData[$field])) {
            if ($binary) {
                array_push($data, "`$target` = ".$updateData[$field]);
            } else {
                $result = _quote($updateData[$field]);
                array_push($data, "`$target` = $result");
            }
        } else {
            array_push($data, "`$target` = null");
        }
    }

}


function updateAccount($updateData, $accountId)
{
    if (array_key_exists('country', $updateData)) {
        $countries = listCountries();
        foreach ($countries as $country) {
            if ($country['id'] === $updateData['country']) {
                $updateData['countryName'] = $country['name'];
                break;
            }
        }
    }

    $sql = "UPDATE `Members` SET ";
    $data = array();
    if (isset($updateData['firstName'])) {
        array_push($data, "`FirstName` = '".$updateData['firstName']."'");
    }
    if (isset($updateData['lastName'])) {
        array_push($data, "`LastName` = '".$updateData['lastName']."'");
    }
    _process_field($data, $updateData, 'middleName', 'MiddleName');
    _process_field($data, $updateData, 'suffix', 'Suffix');
    _process_field($data, $updateData, 'email1', 'Email');
    _process_field($data, $updateData, 'email2', 'Email2');
    _process_field($data, $updateData, 'email3', 'Email3');
    _process_field($data, $updateData, 'phone1', 'Phone');
    _process_field($data, $updateData, 'phone2', 'Phone2');
    _process_field($data, $updateData, 'addressLine1', 'AddressLine1');
    _process_field($data, $updateData, 'addressLine2', 'AddressLine2');
    _process_field($data, $updateData, 'city', 'AddressCity');
    _process_field($data, $updateData, 'state', 'AddressState');
    _process_field($data, $updateData, 'zipCode', 'AddressZipCode');
    _process_field($data, $updateData, 'zipPlus4', 'AddressZipCodeSuffix');
    _process_field($data, $updateData, 'countryName', 'AddressCountry');
    _process_field($data, $updateData, 'province', 'AddressProvince');
    _process_field($data, $updateData, 'preferredFirstName', 'PreferredFirstName');
    _process_field($data, $updateData, 'preferredLastName', 'PreferredLastName');
    _process_field($data, $updateData, 'Deceased', 'Deceased', true);
    _process_field($data, $updateData, 'DoNotContact', 'DoNotContact', true);
    _process_field($data, $updateData, 'EmailOptOut', 'EmailOptOut', true);
    _process_field($data, $updateData, 'Birthdate', 'Birthdate');
    _process_field($data, $updateData, 'Gender', 'Gender');
    _process_field($data, $updateData, 'conComDisplayPhone', 'DisplayPhone', true);

    $sql .= implode(", ", $data);
    $sql .= " WHERE `AccountID` = $accountId;";

    \DB::run($sql);

    if (\ciab\CRM::active()) {
        \ciab\CRM::updateAccount($updateData);
    }

}


function createUser($newemail, $min_accountid = null)
{
    $sel_sql = "SELECT AccountID FROM `Members` WHERE `Email` = '$newemail';";
    $result = DB::run($sel_sql);
    $value = $result->fetch();
    if ($value !== false) {
        return null;
    }

    $aid = null;
    if ($min_accountid !== null && intval($min_accountid) > 0) {
        $aid = intval($min_accountid);
        $sql = "SELECT MAX(AccountID) AS max FROM `Members`;";
        $result = DB::run($sql);
        $value = $result->fetch();
        if ($value !== false) {
            if (intval($value['max']) >= $aid) {
                $aid = null;
            }
        }
    }

    if ($aid !== null) {
        $sql = "INSERT INTO `Members` (`AccountID`, `Email`) VALUES ($aid, '$newemail');";
    } else {
        $sql = "INSERT INTO `Members` (`Email`) VALUES ('$newemail');";
    }
    DB::run($sql);

    $result = DB::run($sel_sql);
    $value = $result->fetch();
    if ($value === false) {
        return null;
    } else {
        return $value['AccountID'];
    }

}


function createPassword(/*.string.*/$id, /*.string.*/$password): string
{
    global $PASSWORDEXPIRE;
    if (isset($PASSWORDEXPIRE) && !empty($PASSWORDEXPIRE)) {
        $duration = $PASSWORDEXPIRE;
    } else {
        $duration = '+1 year';
    }

    $value = lookup_users_by_login($id);
    if (count($value)) {
        $user = $value[0]['AccountID'];
        $expires = _quote(date('Y-m-d H:i', strtotime($duration)));
        $auth = _quote(password_hash($password, PASSWORD_DEFAULT));

        $sql = <<<SQL
            INSERT INTO `Authentication` (AccountID, Authentication, Expires)
            VALUES ($user, $auth, $expires)
SQL;
        DB::run($sql);
        return true;
    }
    return false;

}
