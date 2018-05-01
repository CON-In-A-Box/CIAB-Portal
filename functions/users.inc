<?php

/*.
    require_module 'standard';
    require_module 'json';
.*/


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


function _lookup_user($criteria, $single_result = false, $additional_fields = array())
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
        $user = _parse_user($value, $additional_fields);
        array_push($output['users'], $user);
        $value = $result->fetch();
    }
    return $output;

}


function lookup_users_by_ids($ids)
{
    $str = implode(", ", $ids);
    return _lookup_user("AccountId IN ($str)");

}


function lookup_user_by_id($id)
{
    return _lookup_user("AccountID = $id", true);

}


function lookup_users_by_email($email, $single = true)
{
    return _lookup_user("Email = '$email'", $single);

}


function lookup_users_by_name($name, $single = false)
{
    $names = explode(" ", $name);
    if (count($names) != 2) {
        return ['code' => '400 Bad Request', 'users' => array()];
    }
    return _lookup_user(
        "FirstName = '$names[0]' AND LastName = '$names[1]'",
        $single
    );

}


function lookup_users_by_pref_name($name, $single = false)
{
    $names = explode(" ", $name);
    if (count($names) != 2) {
        return ['code' => '400 Bad Request', 'users' => array()];
    }
    $query = <<<Q
    (PreferredFirstName = '$names[0]' AND PreferredLastName = '$names[1]') OR
    (PreferredFirstName = '$names[0]' AND LastName = '$names[1]') OR
    (FirstName = '$names[0]' AND PreferredLastName = '$names[1]')
Q;
    return _lookup_user($query, $single);

}


function lookup_users_by_badgename($badge, $single_user = false)
{
    global $db;

    $sql = <<<SQL
        SELECT
            AccountID
        FROM
            `Registrations`
        WHERE
            BadgeName = '$badge';
SQL;
    $result = $db->run($sql);
    $value = $result->fetch();
    $users = [];
    while ($value !== false) {
        if ($single_user && count($users) > 1) {
            return array('code' => '409 Conflict', 'users' => array());
        }
        $users[] = lookup_user_by_id($value['AccountID'])['users'][0];
        $value = $result->fetch();
    }

    return array('code' => null, 'users' => $users);

}


function lookup_users_by_key($key, $single = true, $badgename = true)
{
    if (is_numeric($key) ||
        (strtoupper($key)[0] == 'A' && is_numeric(substr($key, 1)))) {
        if (strtoupper($key)[0] == 'A') {
            return lookup_user_by_id(substr($key, 1));
        } else {
            return lookup_user_by_id($key);
        }
    } elseif (strpos($key, '@') !== false) {
        return lookup_users_by_email($key, $single);
    } else {
        $output = lookup_users_by_name($key, $single);
        if (count($output['users']) == 0) {
            $output = lookup_users_by_pref_name($key, $single);
        }
        if ($badgename && count($output['users']) == 0) {
            $output = lookup_users_by_badgename($key, $single);
        }
        return $output;
    }

}


function lookup_users_by_name_email($first, $last, $email, $fields, $single_user = false)
{
    $criteria = array();
    if ($first) {
        array_push($criteria, "FirstName = '$first'");
    }
    if ($last) {
        array_push($criteria, "LastName = '$last'");
    }
    if ($email) {
        array_push($criteria, "Email = '$email'");
    }

    $stmt = implode(" AND ", $criteria);

    return _lookup_user($stmt, $single_user, $fields);

}


function lookup_user_by_login($user)
{
    $output = lookup_users_by_email($user, false);
    if (count($output['users']) == 0) {
        $output = _lookup_user("Login = '$user'", false);
    }
    if (count($output['users']) == 0) {
        return array();
    } else {
        foreach($output['users'] as $key => $user) {
            $output['users'][$key]['AccountID'] = $user['Id'];
        }
        return $output['users'];
    }

}


function pullIndividualAccount($accountId)
{
    $sql = "SELECT * FROM `Members` WHERE AccountId = $accountId;";
    $result = DB::run($sql);
    $value = $result->fetch();
    if ($value === false) {
        return null;
    } else {
        return $value;
    }

}


function listStates()
{
    return array(
        ['code' => 'AL', 'name' => 'ALABAMA'],
        ['code' => 'AK', 'name' => 'ALASKA'],
        ['code' => 'AS', 'name' => 'AMERICAN SAMOA'],
        ['code' => 'AZ', 'name' => 'ARIZONA'],
        ['code' => 'AR', 'name' => 'ARKANSAS'],
        ['code' => 'CA', 'name' => 'CALIFORNIA'],
        ['code' => 'CO', 'name' => 'COLORADO'],
        ['code' => 'CT', 'name' => 'CONNECTICUT'],
        ['code' => 'DE', 'name' => 'DELAWARE'],
        ['code' => 'DC', 'name' => 'DISTRICT OF COLUMBIA'],
        ['code' => 'FM', 'name' => 'FEDERATED STATES OF MICRONESIA'],
        ['code' => 'FL', 'name' => 'FLORIDA'],
        ['code' => 'GA', 'name' => 'GEORGIA'],
        ['code' => 'GU', 'name' => 'GUAM GU'],
        ['code' => 'HI', 'name' => 'HAWAII'],
        ['code' => 'ID', 'name' => 'IDAHO'],
        ['code' => 'IL', 'name' => 'ILLINOIS'],
        ['code' => 'IN', 'name' => 'INDIANA'],
        ['code' => 'IA', 'name' => 'IOWA'],
        ['code' => 'KS', 'name' => 'KANSAS'],
        ['code' => 'KY', 'name' => 'KENTUCKY'],
        ['code' => 'LA', 'name' => 'LOUISIANA'],
        ['code' => 'ME', 'name' => 'MAINE'],
        ['code' => 'MH', 'name' => 'MARSHALL ISLANDS'],
        ['code' => 'MD', 'name' => 'MARYLAND'],
        ['code' => 'MA', 'name' => 'MASSACHUSETTS'],
        ['code' => 'MI', 'name' => 'MICHIGAN'],
        ['code' => 'MN', 'name' => 'MINNESOTA'],
        ['code' => 'MS', 'name' => 'MISSISSIPPI'],
        ['code' => 'MO', 'name' => 'MISSOURI'],
        ['code' => 'MT', 'name' => 'MONTANA'],
        ['code' => 'NE', 'name' => 'NEBRASKA'],
        ['code' => 'NV', 'name' => 'NEVADA'],
        ['code' => 'NH', 'name' => 'NEW HAMPSHIRE'],
        ['code' => 'NJ', 'name' => 'NEW JERSEY'],
        ['code' => 'NM', 'name' => 'NEW MEXICO'],
        ['code' => 'NY', 'name' => 'NEW YORK'],
        ['code' => 'NC', 'name' => 'NORTH CAROLINA'],
        ['code' => 'ND', 'name' => 'NORTH DAKOTA'],
        ['code' => 'MP', 'name' => 'NORTHERN MARIANA ISLANDS'],
        ['code' => 'OH', 'name' => 'OHIO'],
        ['code' => 'OK', 'name' => 'OKLAHOMA'],
        ['code' => 'OR', 'name' => 'OREGON'],
        ['code' => 'PW', 'name' => 'PALAU'],
        ['code' => 'PA', 'name' => 'PENNSYLVANIA'],
        ['code' => 'PR', 'name' => 'PUERTO RICO'],
        ['code' => 'RI', 'name' => 'RHODE ISLAND'],
        ['code' => 'SC', 'name' => 'SOUTH CAROLINA'],
        ['code' => 'SD', 'name' => 'SOUTH DAKOTA'],
        ['code' => 'TN', 'name' => 'TENNESSEE'],
        ['code' => 'TX', 'name' => 'TEXAS'],
        ['code' => 'UT', 'name' => 'UTAH'],
        ['code' => 'VT', 'name' => 'VERMONT'],
        ['code' => 'VI', 'name' => 'VIRGIN ISLANDS'],
        ['code' => 'VA', 'name' => 'VIRGINIA'],
        ['code' => 'WA', 'name' => 'WASHINGTON'],
        ['code' => 'WV', 'name' => 'WEST VIRGINIA'],
        ['code' => 'WI', 'name' => 'WISCONSIN'],
        ['code' => 'WY', 'name' => 'WYOMING'],
        ['code' => 'AE', 'name' => 'ARMED FORCES AFRICA \ CANADA \ EUROPE \ MIDDLE EAST'],
        ['code' => 'AA', 'name' => 'ARMED FORCES AMERICA (EXCEPT CANADA)'],
        ['code' => 'AP', 'name' => 'ARMED FORCES PACIFIC']
    );

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
                array_push($data, "`$target` = '".$updateData[$field]."'");
            }
        } else {
            array_push($data, "`$target` = null");
        }
    }

}


function updateAccount($updateData)
{
    $sql = "UPDATE `Members` SET ";
    $data = array();
    array_push($data, "`FirstName` = '".$updateData['firstName']."'");
    array_push($data, "`LastName` = '".$updateData['lastName']."'");
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
    _process_field($data, $updateData, 'country', 'AddressCountry');
    _process_field($data, $updateData, 'province', 'AddressProvince');
    _process_field($data, $updateData, 'preferredFirstName', 'PreferredFirstName');
    _process_field($data, $updateData, 'preferredLastName', 'PreferredLastName');
    _process_field($data, $updateData, 'Deceased', 'Deceased');
    _process_field($data, $updateData, 'DoNotContact', 'DoNotContact');
    _process_field($data, $updateData, 'EmailOptOut', 'EmailOptOut');
    _process_field($data, $updateData, 'Birthdate', 'Birthdate');
    _process_field($data, $updateData, 'Gender', 'Gender');
    _process_field($data, $updateData, 'conComDisplayPhone', 'DisplayPhone', true);

    $sql .= implode(", ", $data);
    $sql .= " WHERE `AccountID` = ".$updateData['accountId'].";";

    DB::run($sql);

}
