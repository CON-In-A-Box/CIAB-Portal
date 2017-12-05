<?php


function _lookup_user($criteria, $max_users, $additional_fields = array())
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
    'criteria' => $criteria,
    'page' => [
    'currentPage' => 1,
    'pageSize' => $max_users,
    'sortColumn' => 'Last Name',
    'sortDirection' => 'ASC',
    ],
    ];
    if (!empty($additional_fields)) {
        $search['columns']['standardFields'] = array_merge($search['columns']['standardFields'], $additional_fields);
    }
    $results = $Neon->search($search);
    if (isset($results['operationResult']) &&
        $results['operationResult'] == 'SUCCESS' &&
        isset($results['page']['totalResults']) &&
        $results['page']['totalResults'] > 0) {
        if ($results['page']['totalResults'] <= $max_users) {
            $output = ['code' => null, 'users' => array()];
            for ($i = 0; $i < $results['page']['totalResults']; $i++) {
                $user = array();
                $result = $results['searchResults'][$i];

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
                if (!empty($additional_fields)) {
                    foreach ($additional_fields as $field) {
                        $user[$field] = $result[$field];
                    }
                }
                array_push($output['users'], $user);
            }
            return $output;
        } else {
            return array('code' => '409 Conflict', 'users' => array());
        }
    }
    return array('code' => '404 Not Found', 'users' => array());

}


function lookup_user_by_id($id)
{
    return _lookup_user([['Account ID', 'EQUAL', $id]], 1);

}


function lookup_users_by_email($email, $max)
{
    return _lookup_user([['Email', 'EQUAL', $email]], $max);

}


function lookup_users_by_name($name, $max)
{
    $names = explode(" ", $name);
    if (count($names) != 2) {
        return ['code' => '400 Bad Request', 'users' => array()];
    }
    return _lookup_user([['First Name', 'EQUAL', $names[0]],
                         ['Last Name', 'EQUAL', $names[1]]], $max);

}


function lookup_users_by_key($key, $max = 1)
{
    if (is_numeric($key) ||
        (strtoupper($key)[0] == 'A' && is_numeric(substr($key, 1)))) {
        if (strtoupper($key)[0] == 'A') {
            return lookup_user_by_id(substr($key, 1));
        } else {
            return lookup_user_by_id($key);
        }
    } elseif (strpos($key, '@') !== false) {
        return lookup_users_by_email($key, $max);
    } else {
        return lookup_users_by_name($key, $max);
    }

}


function lookup_users_by_name_email($first, $last, $email, $fields, $max_users = 1)
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

    return _lookup_user($criteria, $max_users, $fields);

}
