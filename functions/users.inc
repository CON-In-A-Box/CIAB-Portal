<?php

/*.
    require_module 'standard';
    require_module 'json';
.*/

/*********************************/
/* Neon migration path functions */
/*********************************/


function _quote($value)
{
    if ($value === null || $value === '') {
        return 'NULL';
    } else {
        return MyPDO::quote($value);
    }

}


function _memberDBfromList($person)
{
    $aid = $person['Account ID'];
    $fn = _quote($person['First Name']);
    $ln = _quote($person['Last Name']);
    $mn = _quote($person['Middle Name']);
    $su = _quote($person['Suffix']);
    $em1 = _quote($person['Email 1']);
    $em2 = _quote($person['Email 2']);
    $em3 = _quote($person['Email 3']);
    $ph1 = _quote($person['Phone 1 Full Number (F)']);
    $ph2 = _quote($person['Phone 2 Full Number (F)']);
    $al1 = _quote($person['Address Line 1']);
    $al2 = _quote($person['Address Line 2']);
    $ac = _quote($person['City']);
    $ast = _quote($person['State']);
    $az = _quote($person['Zip Code']);
    $azs = _quote($person['Zip Code Suffix']);
    $aco = _quote($person['Country']);
    $ap = _quote($person['Province']);
    $dc = ($person['Deceased'] === 'Yes') ? 'true' : 'false';
    $dnc = ($person['Do Not Contact'] === 'Yes') ? 'true' : 'false';
    $eoo = ($person['Email Opt-Out'] === 'Yes') ? 'true' : 'false';
    if ($person['DOB Month']) {
        $time = strtotime($person['DOB Month'].'/'.$person['DOB Day'].'/'.$person['DOB Year']);
        $dob = _quote(date('Y-m-d', $time));
    } else {
        $dob = 'NULL';
    }
    $gn = _quote($person['Gender']);
    $pfn = _quote($person['Preferred First Name']);
    $pln = _quote($person['Preferred Last Name']);
    $li = _quote($person['Account Login Name']);
    if ($person['CVGConCom Publish Phone'] === null) {
        $dph = 'NULL';
    } else {
        $dph = $person['CVGConCom Publish Phone'];
    }

    $sql = <<<SQL
        REPLACE INTO `Members` (AccountID, FirstName, MiddleName, LastName,
            Suffix, Email, Email2, Email3, Phone, Phone2,
            AddressLine1, AddressLine2, AddressCity,
            AddressState, AddressZipCode, AddressZipCodeSuffix,
            AddressCountry, AddressProvince, PreferredFirstName,
            PreferredLastName, Login, Deceased, DoNotContact, EmailOptOut,
            Birthdate, Gender, DisplayPhone)
        VALUES ($aid, $fn, $mn,$ln,
                $su, $em1, $em2, $em3, $ph1, $ph2,
                $al1, $al2, $ac,
                $ast, $az, $azs,
                $aco, $ap, $pfn,
                $pln, $li, $dc, $dnc, $eoo,
                $dob, $gn, $dph);

SQL;
    return $sql;

}


function neon_importAccountListToDB($results)
{
    if (!array_key_exists('_MAX_SQL_LENGTH', $GLOBALS)) {
        $sql = "SHOW VARIABLES LIKE 'max_allowed_packet';";
        $result = \DB::run($sql);
        $value = $result->fetch();
        $GLOBALS['_MAX_SQL_LENGTH'] = intval($value['Value']) / 300;
    }

    $sql = "";
    if (isset($results['operationResult']) && $results['operationResult'] == 'SUCCESS') {
        foreach ($results['searchResults'] as $val) {
            $line = _memberDBfromList($val);
            if (strlen($sql) + strlen($line) >= $GLOBALS['_MAX_SQL_LENGTH']) {
                \DB::run($sql);
                $sql = $line;
            } else {
                $sql .= $line;
            }
        }
    }

    if (!empty($sql)) {
        \DB::run($sql);
    }

}


function neon_getContactId($accountId)
{
    // Refresh Account info from Neon to verify update
    $request = [
    'method' => 'account/retrieveIndividualAccount',
    'parameters' => [
    'accountId' => $accountId,
      ],
    ];

    $result = $GLOBALS['Neon']->go($request);
    if (isset($result['operationResult']) && $result['operationResult'] == 'SUCCESS') {
        return $result['individualAccount']['primaryContact']['contactId'];
    }
    return null;

}


function _neon_parse_user($result, $additional_fields)
{
    $user = array();

    $user['Id'] = $result['Account ID'];
    if (!empty($result['Preferred First Name'])) {
        $user['First Name'] = $result['Preferred First Name'];
    } else {
        $user['First Name'] = $result['First Name'];
    }
    if (!empty($result['Preferred Last Name'])) {
        $user['Last Name'] = $result['Preferred Last Name'];
    } else {
        $user['Last Name'] = $result['Last Name'];
    }
    if (class_exists('\\concom\\POSITION') && method_exists('\\concom\\POSITION', 'getConComPosition')) {
        $value = concom\POSITION::getConComPosition($user['Id']);
        if (!empty($value)) {
            $user['ConCom'] = $value;
        }
    }
    if (!empty($result['Email 1'])) {
        $user['Email'] = $result['Email 1'];
    }
    if (!empty($additional_fields)) {
        foreach ($additional_fields as $field) {
            $user[$field] = $result[$field];
        }
    }

    return $user;

}


function _neon_lookup_user($criteria, $single_result = false, $additional_fields = array(), $page = 1, & $output = null)
{
    $preferredFirstName = search_definedFields('Preferred First Name');
    $preferredLastName = search_definedFields('Preferred Last Name');
    $cvgConComPhoneDisplay = search_definedFields('CVGConCom Publish Phone');

    $additional_fields = str_replace(['AddressCity', 'AddressState'], ['City', 'State'], $additional_fields);

    $search = [
    'method' => 'account/listAccounts',
    'columns' => [
    'standardFields' => ['Account ID', 'First Name', 'Last Name', 'Middle Name', 'Suffix', 'City', 'State', 'Email 1', 'Email 2', 'Email 3', 'Phone 1 Full Number (F)', 'Phone 2 Full Number (F)', 'Address Type', 'Address Line 1', 'Address Line 2', 'Zip Code', 'Zip Code Suffix', 'Country', 'Province', 'Account Login Name', 'Deceased', 'Do Not Contact', 'Email Opt-Out', 'DOB Day', 'DOB Month', 'DOB Year', 'Gender'],
    'customFields' => [$preferredFirstName, $preferredLastName, $cvgConComPhoneDisplay],
    ],
    'criteria' => $criteria,
    'page' => [
    'currentPage' => $page,
    'pageSize' => 200,
    'sortColumn' => 'Account ID',
    'sortDirection' => 'ASC',
    ],
    ];
    if (!empty($additional_fields)) {
        foreach ($additional_fields as $field) {
            if (!in_array($field, $search['columns']['standardFields'])) {
                $search['columns']['standardFields'][] = $field;
            }
        }
    }
    $results = $GLOBALS['Neon']->search($search);

    neon_importAccountListToDB($results);

    if (isset($results['operationResult']) && $results['operationResult'] == 'SUCCESS') {
        if ($single_result && $results['page']['totalResults'] > 1) {
            return array('code' => '409 Conflict', 'users' => array());
        } else {
            if ($output === null) {
                $output = ['code' => null, 'users' => array()];
            }
            foreach ($results['searchResults'] as $val) {
                $user = _neon_parse_user($val, $additional_fields);
                array_push($output['users'], $user);
            }
            if ($results['page']['totalPage'] > $page) {
                return _neon_lookup_user($criteria, $single_result, $additional_fields, $page + 1, $output);
            } else {
                return $output;
            }
        }
    }
    return array('code' => '404 Not Found', 'users' => array());

}


function neon_createUser($email, $firstName, $lastName)
{
    loadDefinedFields();

    $output = lookup_users_by_email($email, false);
    if (count($output['users']) !== 0) {
        return null;
    }

    // Formulate the update
    $request = [
    'method' => 'account/createIndividualAccount',
    'parameters' => [
    'originDetail' => $GLOBALS['NEONID'],
    'individualAccount.primaryContact.firstName' => $firstName,
    'individualAccount.primaryContact.lastName' => $lastName,
    'individualAccount.primaryContact.email1' => $email,
    ],
    ];

    $result = $GLOBALS['Neon']->go1($request);

    if (isset($result['operationResult']) && $result['operationResult'] == 'SUCCESS') {
        return $result['accountId'];
    }
    return null;

}


/*************************************/
/* END Neon migration path functions */
/*************************************/


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
    if (array_key_exists('Neon', $GLOBALS)) {
        loadDefinedFields();
        $str = "('".implode("', '", $ids)."')";
        return _neon_lookup_user([['Account ID', 'IN_RANGE', $str]]);
    }
    $str = implode(", ", $ids);
    return _lookup_user("AccountId IN ($str)");

}


function lookup_user_by_id($id)
{
    $id = trim($id);
    if (array_key_exists('Neon', $GLOBALS)) {
        loadDefinedFields();
        return _neon_lookup_user([['Account ID', 'EQUAL', $id]], true);
    }
    return _lookup_user("AccountID = $id", true);

}


function lookup_users_by_email($email, $single = true, $substring = false)
{
    $email = trim($email);
    if (array_key_exists('Neon', $GLOBALS)) {
        loadDefinedFields();
        if ($substring) {
            $operator = 'CONTAIN';
        } else {
            $operator = 'EQUAL';
        }
        return _neon_lookup_user([['Email', $operator, $email]], $single);
    }
    if ($substring) {
        return _lookup_user("Email LIKE '%$email%'", $single);
    } else {
        return _lookup_user("Email = '$email'", $single);
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


function lookup_users_by_name($name, $single = false, $substring = false)
{
    $name = trim(preg_replace('/\s+/', ' ', $name));
    if (array_key_exists('Neon', $GLOBALS)) {
        loadDefinedFields();
        if ($substring) {
            $names = explode(" ", $name);
            if (count($names) < 2) {
                $result = _neon_lookup_user([['First Name', 'CONTAIN', $name]], $single);
                $result2 = _neon_lookup_user([['Last Name', 'CONTAIN', $name]], $single);
                return merge_results($result, $result2);
            } else {
                return _neon_lookup_user(
                    [['First Name', 'EQUAL', $names[0]],
                                     ['Last Name', 'CONTAIN', $names[1]]],
                    $single
                );
            }
        } else {
            $names = explode(" ", $name);
            if (count($names) != 2) {
                return ['code' => '400 Bad Request', 'users' => array()];
            }
            return _neon_lookup_user(
                [['First Name', 'EQUAL', $names[0]],
                                 ['Last Name', 'EQUAL', $names[1]]],
                $single
            );
        }
    }
    $names = explode(" ", $name);
    $i2 = 1;
    if (count($names) < 2) {
        $i2 = 0;
    }
    if ($substring) {
        $query = "FirstName LIKE '%".$names[0]."%' OR LastName LIKE '%".$names[$i2]."%'";
    } else {
        $query = "FirstName = '$names[0]' AND LastName = '$names[1]'";
    }

    return _lookup_user($query, $single);

}


function lookup_users_by_pref_name($name, $single = false, $substring = false)
{
    $name = trim(preg_replace('/\s+/', ' ', $name));
    if (array_key_exists('Neon', $GLOBALS)) {
        loadDefinedFields();
        $preferredFirstName = search_definedFields('Preferred First Name');
        $preferredLastName = search_definedFields('Preferred Last Name');
        if ($substring) {
            $names = explode(" ", $name);
            if (count($names) < 2) {
                $result = _neon_lookup_user([[$preferredFirstName, 'CONTAIN', $name]], $single);
                $result2 = _neon_lookup_user([[$preferredLastName, 'CONTAIN', $name]], $single);
                return merge_results($result, $result2);
            } else {
                $result = _neon_lookup_user(
                    [[$preferredFirstName, 'EQUAL', $names[0]],
                     [$preferredLastName, 'CONTAIN', $names[1]]
                    ],
                    $single
                );
                $result2 = _neon_lookup_user(
                    [['First Name', 'EQUAL', $names[0]],
                     [$preferredLastName, 'CONTAIN', $names[1]]
                    ],
                    $single
                );
                $result = merge_results($result, $result2);
                $result2 = _neon_lookup_user(
                    [['First Name', 'EQUAL', $names[0]],
                     ['Last Name', 'CONTAIN', $names[1]]
                    ],
                    $single
                );
                $result = merge_results($result, $result2);
                $result2 = _neon_lookup_user(
                    [[$preferredFirstName, 'EQUAL', $names[0]],
                     ['Last Name', 'CONTAIN', $names[1]]
                    ],
                    $single
                );
                $result = merge_results($result, $result2);
                return $result;
            }
        } else {
            $names = explode(" ", $name);
            if (count($names) != 2) {
                return ['code' => '400 Bad Request', 'users' => array()];
            }

            /* We have to do a few searchs due to Neon stupidity */
            $output = _neon_lookup_user(
                [[$preferredFirstName, 'EQUAL', $names[0]],
                 [$preferredLastName, 'EQUAL', $names[1]]],
                $single
            );
            if (count($output['users']) != 0) {
                return $output;
            }
            $output = _neon_lookup_user(
                [[$preferredFirstName, 'EQUAL', $names[0]],
                 ['Last Name', 'EQUAL', $names[1]]],
                $single
            );
            if (count($output['users']) != 0) {
                return $output;
            }
            return _neon_lookup_user(
                [['First Name', 'EQUAL', $names[0]],
                 [$preferredLastName, 'EQUAL', $names[1]]],
                $single
            );
        }
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
    } else {
        $query = <<<Q
    (PreferredFirstName = '$names[0]' AND PreferredLastName = '$names[1]') OR
    (PreferredFirstName = '$names[0]' AND LastName = '$names[1]') OR
    (FirstName = '$names[0]' AND PreferredLastName = '$names[1]')
Q;
    }
    return _lookup_user($query, $single);

}


function lookup_users_by_badgename(
    $badge,
    $single_user = false,
    $substring = false
) {
    $badge = trim($badge);
    if ($substring) {
        $sql = <<<SQL
            SELECT
                AccountID
            FROM
                `Registrations`
            WHERE
                BadgeName LIKE '%{$badge}%';
SQL;
    } else {
        $sql = <<<SQL
            SELECT
                AccountID
            FROM
                `Registrations`
            WHERE
                BadgeName = '$badge';
SQL;
    }
    $result = DB::run($sql);
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


function lookup_users_by_key($key, $single = true, $badgename = true, $substring = false)
{
    $key = trim(preg_replace('/\s+/', ' ', $key));
    if ($substring) {
        $output = ['code' => '404 Not Found', 'users' => array()];
        if (is_numeric($key) ||
            (strtoupper($key)[0] == 'A' && is_numeric(substr($key, 1)))) {
            if (strtoupper($key)[0] == 'A') {
                $result = lookup_user_by_id(substr($key, 1));
                $output = merge_results($output, $result);
            } else {
                $result = lookup_user_by_id($key);
                $output = merge_results($output, $result);
            }
        }
        $result = lookup_users_by_email($key, $single, true);
        $output = merge_results($output, $result);
        $result = lookup_users_by_name($key, $single, true);
        $output = merge_results($output, $result);
        $result = lookup_users_by_pref_name($key, $single, true);
        $output = merge_results($output, $result);
        $result = lookup_users_by_badgename($key, $single, true);
        $output = merge_results($output, $result);
        return $output;
    } else {
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

}


function lookup_users_by_name_email($first, $last, $email, $fields, $single_user = false, $substring = false)
{
    $criteria = array();
    if (array_key_exists('Neon', $GLOBALS)) {
        loadDefinedFields();
        if ($first) {
            if ($substring) {
                array_push($criteria, ['First Name', 'CONTAIN', $first]);
            } else {
                array_push($criteria, ['First Name', 'EQUAL', $first]);
            }
        }
        if ($last) {
            if ($substring) {
                array_push($criteria, ['Last Name', 'CONTAIN', $last]);
            } else {
                array_push($criteria, ['Last Name', 'EQUAL', $last]);
            }
        }
        if ($email) {
            if ($substring) {
                array_push($criteria, ['Email', 'CONTAIN', $email]);
            } else {
                array_push($criteria, ['Email', 'EQUAL', $email]);
            }
        }

        return _neon_lookup_user($criteria, $single_user, $fields);
    }
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


function lookup_user_by_login($user, $substring = false)
{
    $output = lookup_users_by_email($user, false);
    if (count($output['users']) == 0) {
        if (array_key_exists('Neon', $GLOBALS)) {
            loadDefinedFields();
            if ($substring) {
                $output = _neon_lookup_user([['Account Login Name', 'CONTAIN', $user]], false);
            } else {
                $output = _neon_lookup_user([['Account Login Name', 'EQUAL', $user]], false);
            }
        } else {
            if ($substring) {
                $output = _lookup_user("Login LIKE '%$user%'", false);
            } else {
                $output = _lookup_user("Login = '$user'", false);
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


function neon_updateAccount($updateData)
{
    global $_SESSION;

    $updateData['contactId'] = neon_getContactId($updateData['accountId']);

    // Formulate the update
    $request = [
    'method' => 'account/updateIndividualAccount',
    'parameters' => [
    'individualAccount.accountId' => $updateData['accountId'],
    'individualAccount.primaryContact.contactId' => $updateData['contactId'],
    'individualAccount.primaryContact.firstName' => $updateData['firstName'],
    'individualAccount.primaryContact.middleName' => $updateData['middleName'],
    'individualAccount.primaryContact.lastName' => $updateData['lastName'],
    'individualAccount.primaryContact.suffix' => $updateData['suffix'],
    'individualAccount.primaryContact.email1' => $updateData['email1'],
    'individualAccount.primaryContact.email2' => $updateData['email2'],
    'individualAccount.primaryContact.email3' => $updateData['email3'],
    'individualAccount.primaryContact.phone1' => $updateData['phone1'],
    'individualAccount.primaryContact.phone2' => $updateData['phone2'],
    'individualAccount.primaryContact.addresses.address.addressType.Name' => 'Home',
    'individualAccount.primaryContact.addresses.address.addressId' => $updateData['addressId'],
    'individualAccount.primaryContact.addresses.address.isPrimaryAddress' => 'true',
    'individualAccount.primaryContact.addresses.address.addressLine1' => $updateData['addressLine1'],
    'individualAccount.primaryContact.addresses.address.addressLine2' => $updateData['addressLine2'],
    'individualAccount.primaryContact.addresses.address.addressLine3' => '',
    'individualAccount.primaryContact.addresses.address.addressLine4' => '',
    'individualAccount.primaryContact.addresses.address.city' => $updateData['city'],
    'individualAccount.primaryContact.addresses.address.state.code' => $updateData['state'],
    'individualAccount.primaryContact.addresses.address.zipCode' => $updateData['zipCode'],
    'individualAccount.primaryContact.addresses.address.zipCodeSuffix' => $updateData['zipPlus4'],
    'individualAccount.primaryContact.addresses.address.country.id' => $updateData['country'],
    'individualAccount.primaryContact.addresses.address.province' => $updateData['province'],
    ],
    ];

    $preferredFirstName = search_definedFields('Preferred First Name');
    $preferredLastName = search_definedFields('Preferred Last Name');
    $cvgConComPhoneDisplay = search_definedFields('CVGConCom Publish Phone');

    // add custom field data, watch for updated fields, if empty, drop them to clear them from Neon

    $key = 0;
    if (!empty($updateData['preferredFirstName'])) {
        $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldId['.$key.']'] = $preferredFirstName;
        $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId['.$key.']'] = '';
        $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldValue['.$key.']'] = $updateData['preferredFirstName'];
        $key++;
    }
    if (!empty($updateData['preferredLastName'])) {
        $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldId['.$key.']'] = $preferredLastName;
        $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId['.$key.']'] = '';
        $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldValue['.$key.']'] = $updateData['preferredLastName'];
        $key++;
    }
    if (!empty($updateData['conComDisplayPhone'])) {
        $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldId['.$key.']'] = $cvgConComPhoneDisplay;
        $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId['.$key.']'] = '';
        $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldValue['.$key.']'] = $updateData['conComDisplayPhone'];
    }

    /* Password setting */
    if (!empty($updateData['password'])) {
        if (empty($updateData['login'])) {
            $updateData['login'] = $updateData['email1'];
        }
        $request['parameters']['individualAccount.login.username'] = $updateData['login'];
        $request['parameters']['individualAccount.login.password'] = $updateData['password'];
    }

    return $GLOBALS['Neon']->go1($request);

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


function updateAccount($updateData)
{
    $countries = listCountries();
    foreach ($countries as $country) {
        if ($country['id'] === $updateData['country']) {
            $updateData['countryName'] = $country['name'];
            break;
        }
    }

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
    $sql .= " WHERE `AccountID` = ".$updateData['accountId'].";";

    \DB::run($sql);

    if (array_key_exists('Neon', $GLOBALS)) {
        loadDefinedFields();
        neon_updateAccount($updateData);
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
