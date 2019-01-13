<?php

/*.
    require_module 'standard';
    require_module 'json';
.*/


function _parse_user($result, $additional_fields)
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
        $user['ConCom'] = concom\POSITION::getConComPosition($user['Id']);
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


function _lookup_user($criteria, $single_result = false, $additional_fields = array(), $page = 1, & $output = null)
{
    global $Neon;
    $preferredFirstName = search_definedFields('Preferred First Name');
    $preferredLastName = search_definedFields('Preferred Last Name');

    $search = [
    'method' => 'account/listAccounts',
    'columns' => [
    'standardFields' => ['Account ID', 'First Name', 'Last Name', 'Email 1'],
    'customFields' => [$preferredFirstName, $preferredLastName],
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
        $search['columns']['standardFields'] = array_merge($search['columns']['standardFields'], $additional_fields);
    }
    $results = $Neon->search($search);
    if (isset($results['operationResult']) && $results['operationResult'] == 'SUCCESS') {
        if ($single_result && $results['page']['totalResults'] > 1) {
            return array('code' => '409 Conflict', 'users' => array());
        } else {
            if ($output === null) {
                $output = ['code' => null, 'users' => array()];
            }
            foreach ($results['searchResults'] as $val) {
                $user = _parse_user($val, $additional_fields);
                array_push($output['users'], $user);
            }
            if ($results['page']['totalPage'] > $page) {
                return _lookup_user($criteria, $single_result, $additional_fields, $page + 1, $output);
            } else {
                return $output;
            }
        }
    }
    return array('code' => '404 Not Found', 'users' => array());

}


function lookup_users_by_ids($id)
{
    return _lookup_user([['Account ID', 'IN_RANGE', $id]]);

}


function lookup_user_by_id($id)
{
    return _lookup_user([['Account ID', 'EQUAL', $id]], true);

}


function lookup_users_by_email($email, $single = true)
{
    return _lookup_user([['Email', 'EQUAL', $email]], $single);

}


function lookup_users_by_name($name, $single = false)
{
    $names = explode(" ", $name);
    if (count($names) != 2) {
        return ['code' => '400 Bad Request', 'users' => array()];
    }
    return _lookup_user(
        [['First Name', 'EQUAL', $names[0]],
                         ['Last Name', 'EQUAL', $names[1]]],
        $single
    );

}


function lookup_users_by_pref_name($name, $single = false)
{
    $preferredFirstName = search_definedFields('Preferred First Name');
    $preferredLastName = search_definedFields('Preferred Last Name');
    $names = explode(" ", $name);
    if (count($names) != 2) {
        return ['code' => '400 Bad Request', 'users' => array()];
    }

    /* We have to do a few searchs due to Neon stupidity */
    $output = _lookup_user(
        [[$preferredFirstName, 'EQUAL', $names[0]],
         [$preferredLastName, 'EQUAL', $names[1]]],
        $single
    );
    if (count($output['users']) != 0) {
        return $output;
    }
    $output = _lookup_user(
        [[$preferredFirstName, 'EQUAL', $names[0]],
         ['Last Name', 'EQUAL', $names[1]]],
        $single
    );
    if (count($output['users']) != 0) {
        return $output;
    }
    return _lookup_user(
        [['First Name', 'EQUAL', $names[0]],
         [$preferredLastName, 'EQUAL', $names[1]]],
        $single
    );

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
        array_push($criteria, ['First Name', 'EQUAL', $first]);
    }
    if ($last) {
        array_push($criteria, ['Last Name', 'EQUAL', $last]);
    }
    if ($email) {
        array_push($criteria, ['Email', 'EQUAL', $email]);
    }

    return _lookup_user($criteria, $single_user, $fields);

}


function lookup_user_by_login($user)
{
    $output = lookup_users_by_email($user, false);
    if (count($output['users']) == 0) {
        $output = _lookup_user([['Account Login Name', 'EQUAL', $user]], false);
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
    global $Neon;
    // Refresh Account info from Neon to verify update
    $request = [
    'method' => 'account/retrieveIndividualAccount',
    'parameters' => [
    'accountId' => $accountId,
      ],
    ];

    $result = $Neon->go($request);
    if (isset($result['operationResult']) && $result['operationResult'] == 'SUCCESS') {
        return $result;
    } else {
        sendError("Could not retrieve IndividualAccount for account ".$accountId." in ".__FILE__, $result);
        return null;
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


function updateAccount($updateData)
{
    global $Neon, $_SESSION;

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

    $i = count($_SESSION['accountInfo']['individualAccount']['customFieldDataList']['customFieldData']);
    // Verify fieldId is in place - potential for manual add to lottery or override
    $key = array_search($preferredFirstName, array_column($_SESSION['accountInfo']['individualAccount']['customFieldDataList']['customFieldData'], 'fieldId'));
    if ($key === false && !empty($updateData['preferredFirstName'])) {
        $_SESSION['accountInfo']['individualAccount']['customFieldDataList']['customFieldData'][$i]['fieldId'] = $preferredFirstName;
        $_SESSION['accountInfo']['individualAccount']['customFieldDataList']['customFieldData'][$i]['fieldValue'] = $updateData['preferredFirstName'];
        $i++;
    }
    unset($key);
    $key = array_search($preferredLastName, array_column($_SESSION['accountInfo']['individualAccount']['customFieldDataList']['customFieldData'], 'fieldId'));
    if ($key === false && !empty($updateData['preferredLastName'])) {
        $_SESSION['accountInfo']['individualAccount']['customFieldDataList']['customFieldData'][$i]['fieldId'] = $preferredLastName;
        $_SESSION['accountInfo']['individualAccount']['customFieldDataList']['customFieldData'][$i]['fieldValue'] = $updateData['preferredLastName'];
        $i++;
    }
    $key = array_search($cvgConComPhoneDisplay, array_column($_SESSION['accountInfo']['individualAccount']['customFieldDataList']['customFieldData'], 'fieldId'));
    if ($key === false && !empty($updateData['conComDisplayPhone'])) {
        $_SESSION['accountInfo']['individualAccount']['customFieldDataList']['customFieldData'][$i]['fieldId'] = $cvgConComPhoneDisplay;
        $_SESSION['accountInfo']['individualAccount']['customFieldDataList']['customFieldData'][$i]['fieldValue'] = $updateData['conComDisplayPhone'];
        $i++;
    }
    unset($key);
    unset($i);

    // add custom field data, watch for updated fields, if empty, drop them to clear them from Neon
    foreach ($_SESSION['accountInfo']['individualAccount']['customFieldDataList']['customFieldData'] as $key => $val) {
        if ($val['fieldId'] == $preferredFirstName) {
            if (!empty($updateData['preferredFirstName'])) {
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldId['.$key.']'] = $val['fieldId'];
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId['.$key.']'] = '';
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldValue['.$key.']'] = $updateData['preferredFirstName'];
            }
        } elseif ($val['fieldId'] == $preferredLastName) {
            if (!empty($updateData['preferredLastName'])) {
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldId['.$key.']'] = $val['fieldId'];
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId['.$key.']'] = '';
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldValue['.$key.']'] = $updateData['preferredLastName'];
            }
        } elseif ($val['fieldId'] == $cvgConComPhoneDisplay) {
            if (!empty($updateData['conComDisplayPhone'])) {
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldId['.$key.']'] = $val['fieldId'];
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId['.$key.']'] = '';
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldValue['.$key.']'] = $updateData['conComDisplayPhone'];
            }
        } else {
            $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldId['.$key.']'] = $val['fieldId'];
            if (isset($val['fieldValue'])) {
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId['.$key.']'] = '';
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldValue['.$key.']'] = $val['fieldValue'];
            } elseif (isset($val['fieldOptionId'])) {
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldOptionId['.$key.']'] = $val['fieldOptionId'];
                $request['parameters']['individualAccount.customFieldDataList.customFieldData.fieldValue['.$key.']'] = '';
            }
        }
    }

    return $Neon->go1($request);

}
