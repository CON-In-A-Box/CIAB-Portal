<?php

require_once(__DIR__."/../functions/functions.inc");
require_once(__DIR__."/../functions/database.inc");


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


function _quote($value)
{
    if ($value === null || $value === '') {
        return 'NULL';
    } else {
        return MyPDO::quote($value);
    }

}


function _updateMemberSql($person, $add)
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

    if ($add) {
        $sql = <<<SQL
            INSERT INTO `Members` (AccountID, FirstName, MiddleName, LastName,
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
        $GLOBALS['MAX_PACKET'] = intval($value['Value']) / 160;
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
