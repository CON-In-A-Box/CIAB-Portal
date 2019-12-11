<?php

require_once(__DIR__."/../functions/functions.inc");
require_once(__DIR__."/../functions/database.inc");
require_once(__DIR__."/../functions/users.inc");


function _loadEventCustomFields()
{
    global $Neon;

    $fields = [];
    $fields['definedFields'] = [];

    // Custom Field Data for Session - Parse it to an easy to use array
    $request = [
    'method' => 'common/listCustomFields',
    'parameters' => [
    'searchCriteria.component' => "Account",
        ],
        ];
    $result = $Neon->go($request);
    if (isset($result['operationResult']) && $result['operationResult'] == 'SUCCESS') {
        $fields['definedFields']['customField'] = $result['customFields']['customField'];
        foreach ($result['customFields']['customField'] as $val) {
            $fields['definedFields'][$val['fieldId']] = $val['fieldName'];
        }
    } else {
        die("Impossible error during Defined Custom Fields Download");
    }
    return $fields;

}


function _lookup_members($fields, $page = 1, $output = null, $all = true)
{
    global $Neon;

    $pfn = array_search('Preferred First Name', $fields['definedFields']);
    $pln = array_search('Preferred Last Name', $fields['definedFields']);
    $dph = array_search('CVGConCom Publish Phone', $fields['definedFields']);

    $search = [
    'method' => 'account/listAccounts',
    'columns' => [
    'standardFields' => ['Account ID', 'First Name', 'Last Name', 'Middle Name', 'Suffix', 'City', 'State', 'Email 1', 'Email 2', 'Email 3', 'Phone 1 Full Number (F)', 'Phone 2 Full Number (F)', 'Address Type', 'Address Line 1', 'Address Line 2', 'Zip Code', 'Zip Code Suffix', 'Country', 'Province', 'Account Login Name', 'Deceased', 'Do Not Contact', 'Email Opt-Out', 'DOB Day', 'DOB Month', 'DOB Year', 'Gender'],
    'customFields' => [$pfn, $pln, $dph],
    ],
    'page' => [
    'currentPage' => $page,
    'pageSize' => 200,
    'sortColumn' => 'Account ID',
    'sortDirection' => 'ASC'
      ],
    ];
    $results = $Neon->search($search);

    if (isset($results['operationResult']) && $results['operationResult'] == 'SUCCESS') {
        if ($output === null) {
            $output = ['code' => null, 'members' => array()];
        }

        foreach ($results['searchResults'] as $val) {
            /* check for dummy accounts */
            if ((!isset($val['First Name']) || $val['First Name'] === '') &&
                (!isset($val['Last Name']) || $val['Last Name'] === '') &&
                (!isset($val['Email 1']) || $val['Email 1'] === '')) {
                continue;
            }
            array_push($output['members'], $val);
        }
        if ($all && $results['page']['totalPage'] > $page) {
            echo $page.'/'.$results['page']['totalPage']."\n";
            return _lookup_members($fields, $page + 1, $output);
        } else {
            return $output;
        }
    }
    return array('code' => '404 Not Found', 'members' => array());

}


function _updateMemberSql($person, $add)
{
    if ($add) {
        return _memberDBfromList($person);
    } else {
        return "";
    }

}


function _import_page_of_people($people, $page)
{
    $total = 0;
    $command = "";

    if (!array_key_exists('MAX_PACKET', $GLOBALS)) {
        $sql = "SHOW VARIABLES LIKE 'max_allowed_packet';";
        $result = \DB::run($sql);
        $value = $result->fetch();
        $GLOBALS['MAX_PACKET'] = intval($value['Value']) / 300;
    }

    $key = [];
    foreach ($people['members'] as $person) {
        $key[] = $person['Account ID'];
    }

    $set = '('.implode(', ', $key).')';

    $sql = <<<SQL
        SELECT AccountID
        FROM `Members`
        WHERE AccountID in $set;
SQL;
    $result = DB::run($sql);

    $exists = [];
    $value = $result->fetch();
    while ($value) {
        $exists[] = $value['AccountID'];
        $value = $result->fetch();
    }

    foreach ($people['members'] as $person) {
        $key = $person['Account ID'];
        if (in_array($key, $exists)) {
            $line = _updateMemberSql($person, false);
        } else {
            $line = _updateMemberSql($person, true);
        }

        if (strlen($command) + strlen($line) > $GLOBALS['MAX_PACKET']) {
            \DB::run($command);
            $command = $line;
        } else {
            $command .= $line;
        }

        $total ++;
    }
    if (!empty($command)) {
        \DB::run($command);
    }
    return $total;

}
