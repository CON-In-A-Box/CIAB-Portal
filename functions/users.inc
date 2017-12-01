<?php


function lookup_user_by_id($id)
{
    global $Neon;
    $preferredFirstName = search_definedFields('Preferred First Name');
    $preferredLastName = search_definedFields('Preferred Last Name');
    $currentConCom = search_definedFields('CVGConCom Current Position');

    $search = [
    'method' => 'account/listAccounts',
    'columns' => [
    'standardFields' => ['Account ID', 'First Name', 'Last Name', 'Email 1'],
    'customFields' => [$preferredFirstName, $preferredLastName, $currentConCom],
    ],
    'criteria' => [
    ['Account ID', 'EQUAL', $id,],
    ],
    'page' => [
    'currentPage' => 1,
    'pageSize' => 2,
    'sortColumn' => 'Last Name',
    'sortDirection' => 'ASC',
    ],
    ];
    $results = $Neon->search($search);
    if (isset($results['operationResult']) &&
        $results['operationResult'] == 'SUCCESS' &&
        isset($results['page']['totalResults']) &&
        $results['page']['totalResults'] == 1) {
        $user = array();
        $result = $results['searchResults'][0];

        $user['Id'] = $id;
        if (!empty($result['Preferred First Name'])) {
            $user['First Name'] = $result['Preferred First Name'];
        } else {
            $user['First Name'] = $result['First Name'];
        }
        echo " ";
        if (!empty($result['Preferred Last Name'])) {
            $user['Last Name'] = $result['Preferred Last Name'];
        } else {
            $user['Last Name'] = $result['Last Name'];
        }
        if (!empty($result['CVGConCom Current Position'])) {
            $user['ConCom'] = $result['CVGConCom Current Position'];
        }
        if (!empty($result['Email 1'])) {
            $user['Email'] = $result['Email 1'];
        }
        return array($user);
    }
    return array();

}


function lookup_users_by_email($email)
{
    global $Neon;
    $preferredFirstName = search_definedFields('Preferred First Name');
    $preferredLastName = search_definedFields('Preferred Last Name');
    $currentConCom = search_definedFields('CVGConCom Current Position');

    $search = [
    'method' => 'account/listAccounts',
    'columns' => [
    'standardFields' => ['Account ID', 'First Name', 'Last Name', 'Email 1'],
    'customFields' => [$preferredFirstName, $preferredLastName, $currentConCom],
    ],
    'criteria' => [
    ['Email', 'EQUAL', $email,],
    ],
    'page' => [
    'currentPage' => 1,
    'pageSize' => 2,
    'sortColumn' => 'Last Name',
    'sortDirection' => 'ASC',
    ],
    ];
    $results = $Neon->search($search);
    if (isset($results['operationResult']) &&
        $results['operationResult'] == 'SUCCESS' &&
        isset($results['page']['totalResults']) &&
        $results['page']['totalResults'] == 1) {
        $user = array();
        $result = $results['searchResults'][0];

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
        if (!empty($result['CVGConCom Current Position'])) {
            $user['ConCom'] = $result['CVGConCom Current Position'];
        }
        if (!empty($result['Email 1'])) {
            $user['Email'] = $result['Email 1'];
        }
        return array($user);
    }
    return array();

}


function lookup_users_by_name($name)
{
    global $Neon;
    $preferredFirstName = search_definedFields('Preferred First Name');
    $preferredLastName = search_definedFields('Preferred Last Name');
    $currentConCom = search_definedFields('CVGConCom Current Position');

    $names = explode(" ", $name);
    if (count($names) != 2) {
        return array();
    }

    $search = [
    'method' => 'account/listAccounts',
    'columns' => [
    'standardFields' => ['Account ID', 'First Name', 'Last Name', 'Email 1'],
    'customFields' => [$preferredFirstName, $preferredLastName, $currentConCom],
    ],
    'criteria' => [
    ['First Name', 'EQUAL', $names[0]],
    ['Last Name', 'EQUAL', $names[1]],
    ],
    'page' => [
    'currentPage' => 1,
    'pageSize' => 2,
    'sortColumn' => 'Last Name',
    'sortDirection' => 'ASC',
    ],
    ];
    $results = $Neon->search($search);
    if (isset($results['operationResult']) &&
        $results['operationResult'] == 'SUCCESS' &&
        isset($results['page']['totalResults']) &&
        $results['page']['totalResults'] == 1) {
        $user = array();
        $result = $results['searchResults'][0];

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
        if (!empty($result['CVGConCom Current Position'])) {
            $user['ConCom'] = $result['CVGConCom Current Position'];
        }
        if (!empty($result['Email 1'])) {
            $user['Email'] = $result['Email 1'];
        }
        return array($user);
    }
    return array();

}


function lookup_users_by_key($key)
{
    if (is_numeric($key) ||
        (strtoupper($key)[0]=='A' && is_numeric(substr($key, 1)))) {
        if (strtoupper($key)[0] == 'A') {
            return lookup_user_by_id(substr($key, 1));
        } else {
            return lookup_user_by_id($key);
        }
    } else if (strpos($key, '@') !== false) {
        return lookup_users_by_email($key);
    } else {
        return lookup_users_by_name($key);
    }
}