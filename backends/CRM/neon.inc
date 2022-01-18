<?php


/*
 *  This is a email back end module using NEON as the CRM
 */

/*.
    require_module 'standard';
.*/

namespace ciab\backend;

require_once(__DIR__.'/neon/neon_api.inc');

class CRM
{

    private /*.Neon.*/ $neon = null;

    private $set = array();


    public function __construct()
    {
        global $NEONID, $NEONKEY, $NEONTRIAL;
        $this->neon = new \Neon();
        $this->loadDefinedFields();

    }


    private function loadDefinedFields()
    {
        global $_SESSION, $ADMINACCOUNTS;

        // Custom Field Data for Session - Parse it to an easy to use array
        $request = [
        'method' => 'common/listCustomFields',
        'parameters' => [
        'searchCriteria.component' => "Account",
        ],
        ];
        $result = $this->neon->go($request);
        if (isset($result['operationResult']) && $result['operationResult'] == 'SUCCESS') {
            $_SESSION['definedFields']['customField'] = $result['customFields']['customField'];
            foreach ($result['customFields']['customField'] as $val) {
                $_SESSION['definedFields'][$val['fieldId']] = $val['fieldName'];
                if (isset($val['fieldOptions'])) {
                    foreach ($val['fieldOptions']['fieldOption'] as $fval) {
                        $_SESSION['definedFields'][$fval['id']] = $fval['name'];
                    }
                    unset($fval);
                    if ($val['fieldName'] == "Lottery Status") {
                        foreach ($val['fieldOptions']['fieldOption'] as $fval) {
                            $_SESSION['definedFields']['Lottery Status'][$fval['id']] = $fval['name'];
                        }
                        unset($fval);
                    } elseif ($val['fieldName'] == "Bed Preference") {
                        foreach ($val['fieldOptions']['fieldOption'] as $fval) {
                            $_SESSION['definedFields']['Bed Preference'][$fval['id']] = $fval['name'];
                        }
                        unset($fval);
                    } elseif ($val['fieldName'] == "Hotel Qualifier") {
                        foreach ($val['fieldOptions']['fieldOption'] as $fval) {
                            $_SESSION['definedFields']['Hotel Qualifier'][$fval['id']] = $fval['name'];
                        }
                        unset($fval);
                    }
                }
            }
            unset($val);
        } else {
            die("Impossible error during Defined Custom Fields Download");
        }

    }


    private function searchDefinedFields($key)
    {
        if (isset($_SESSION) && array_key_exists('definedFields', $_SESSION)) {
            return array_search($key, $_SESSION['definedFields']);
        }
        return false;

    }


    public function active()
    {
        return ($this->neon != null);

    }


    public function hasWebhooks()
    {
        return true;

    }


    public function hasEvents()
    {
        return true;

    }


    public function hasUpdates()
    {
        return true;

    }


    public function logout()
    {
        $this->neon->logout();

    }


    public function checkForUserAndPasswordMatch($user, $password)
    {
        $request = [
        'method' => 'common/authenticateUser',
        'parameters' => [
        'username' => $user,
        'password' => $password,
        ],
        ];
        $result = $this->neon->go($request);
        if ($result['operationResult'] == 'SUCCESS') {
            $result['FirstName'] = $result['name'];
            $result['AccountID'] = $result['accountId'];
            return $result;
        }

    }


    private function quote($value)
    {
        if ($value === null || $value === '') {
            return 'NULL';
        } else {
            return \MyPDO::quote($value);
        }

    }


    private function duplicateEmail($aid, $email)
    {
        $sql = <<<SQL
SELECT *
FROM `Members`
WHERE `AccountID` != $aid AND
`Email` = '$email'
ORDER BY `AccountID` ASC
SQL;
        $result = \DB::run($sql);
        $data = $result->fetchAll();
        if (empty($data)) {
            return null;
        }
        return $data[0]['AccountID'];

    }


    private function memberDBfromList($person)
    {
        $aid = $person['Account ID'];
        $fn = $this->quote($person['First Name']);
        $ln = $this->quote($person['Last Name']);
        $mn = $this->quote($person['Middle Name']);
        $su = $this->quote($person['Suffix']);
        $existing = $this->duplicateEmail($aid, $person['Email 1']);
        if ($existing !== null) {
            $em1 = 'NULL';
            $dep = $existing;
        } else {
            $em1 = $this->quote($person['Email 1']);
            $dep = 'NULL';
        }
        $em2 = $this->quote($person['Email 2']);
        $em3 = $this->quote($person['Email 3']);
        $ph1 = $this->quote($person['Phone 1 Full Number (F)']);
        $ph2 = $this->quote($person['Phone 2 Full Number (F)']);
        $al1 = $this->quote($person['Address Line 1']);
        $al2 = $this->quote($person['Address Line 2']);
        $ac = $this->quote($person['City']);
        $ast = $this->quote($person['State']);
        $az = $this->quote($person['Zip Code']);
        $azs = $this->quote($person['Zip Code Suffix']);
        $aco = $this->quote($person['Country']);
        $ap = $this->quote($person['Province']);
        $dc = ($person['Deceased'] === 'Yes') ? 'true' : 'false';
        $dnc = ($person['Do Not Contact'] === 'Yes') ? 'true' : 'false';
        $eoo = ($person['Email Opt-Out'] === 'Yes') ? 'true' : 'false';
        if ($person['DOB Month']) {
            $time = strtotime($person['DOB Month'].'/'.$person['DOB Day'].'/'.$person['DOB Year']);
            $dob = $this->quote(date('Y-m-d', $time));
        } else {
            $dob = 'NULL';
        }
        $gn = $this->quote($person['Gender']);
        $pfn = $this->quote($person['Preferred First Name']);
        $pln = $this->quote($person['Preferred Last Name']);
        $li = $this->quote($person['Account Login Name']);
        if ($person['CVGConCom Publish Phone'] === null) {
            $dph = 'NULL';
        } else {
            $dph = $person['CVGConCom Publish Phone'];
        }

        $sql = <<<SQL
INSERT INTO `Members` (AccountID, FirstName, MiddleName, LastName,
Suffix, Email, Email2, Email3, Phone, Phone2,
AddressLine1, AddressLine2, AddressCity,
AddressState, AddressZipCode, AddressZipCodeSuffix,
AddressCountry, AddressProvince, PreferredFirstName,
PreferredLastName, Login, Deceased, DoNotContact, EmailOptOut,
Birthdate, Gender, DisplayPhone, dependentOnID)
VALUES ($aid, $fn, $mn,$ln,
$su, $em1, $em2, $em3, $ph1, $ph2,
$al1, $al2, $ac,
$ast, $az, $azs,
$aco, $ap, $pfn,
$pln, $li, $dc, $dnc, $eoo,
$dob, $gn, $dph, $dep)
ON DUPLICATE KEY UPDATE
FirstName=$fn, MiddleName=$mn, LastName=$ln, Suffix=$su,
Email2=$em2, Email3=$em3, Phone=$ph1, Phone2=$ph2,
AddressLine1=$al1, AddressLine2=$al2, AddressCity=$ac,
AddressState=$ast, AddressZipCode=$az,
AddressZipCodeSuffix=$azs, AddressCountry=$aco,
AddressProvince=$ap, PreferredFirstName=$pfn,
PreferredLastName=$pln, Login=$li, Deceased=$dc,
DoNotContact=$dnc, EmailOptOut=$eoo,
Birthdate=$dob, Gender=$gn, DisplayPhone=$dph;

SQL;
        return $sql;

    }


    private function importAccountListToDB($results)
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
                $line = $this->memberDBfromList($val);
                if (strlen($sql) + strlen($line) >= $GLOBALS['_MAX_SQL_LENGTH']) {
                    \DB::run($sql);
                    $sql = $line;
                } else {
                    $sql .= $line;
                }
            }
        }
        try {
            if (!empty($sql)) {
                \DB::run($sql);
            }
        } catch (exception $e) {
            error_log($e->getMessage());
            error_log($sql);
        }

    }


    private function getContactId($accountId)
    {
// Refresh Account info from Neon to verify update
        $request = [
        'method' => 'account/retrieveIndividualAccount',
        'parameters' => [
        'accountId' => $accountId,
        ],
        ];

        $result = $this->neon->go($request);
        if (isset($result['operationResult']) && $result['operationResult'] == 'SUCCESS') {
            return $result['individualAccount']['primaryContact']['contactId'];
        }
        return null;

    }


    private function neonParseUser($result, $additional_fields)
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
            $value = \concom\POSITION::getConComPosition($user['Id']);
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


    private function lookupUser($criteria, $single_result = false, $additional_fields = array(), $page = 1, &$output = null)
    {
        $preferredFirstName = $this->searchDefinedFields('Preferred First Name');
        $preferredLastName = $this->searchDefinedFields('Preferred Last Name');
        $cvgConComPhoneDisplay = $this->searchDefinedFields('CVGConCom Publish Phone');

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
        $results = $this->neon->search($search);

        $this->importAccountListToDB($results);

        if (isset($results['operationResult']) && $results['operationResult'] == 'SUCCESS') {
            if ($single_result && $results['page']['totalResults'] > 1) {
                return array('code' => '409 Conflict', 'users' => array());
            } else {
                if ($output === null) {
                    $output = ['code' => null, 'users' => array()];
                }
                foreach ($results['searchResults'] as $val) {
                    $user = $this->neonParseUser($val, $additional_fields);
                    array_push($output['users'], $user);
                }
                if ($results['page']['totalPage'] > $page) {
                    return $this->lookupUser($criteria, $single_result, $additional_fields, $page + 1, $output);
                } else {
                    return $output;
                }
            }
        }
        return array('code' => '404 Not Found', 'users' => array());

    }


    private function mergeResults($result1, $result2)
    {
        $this->set = array();
        $output = ['code' => '404 Not Found', 'users' => array()];

        if ($result1['code'] === null) {
            $output['code'] = null;
            $output['users'] = array_merge($output['users'], $result1['users']);
            foreach ($result1['users'] as $u) {
                $this->set[] = $u['Id'];
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


    public function lookupUsersByIds($ids, $single, $fields)
    {
        if (is_array($ids)) {
            $str = "('".implode("', '", $ids)."')";
        } else {
            $str = "('".strval($ids)."')";
        }
        return $this->lookupUser([['Account ID', 'IN_RANGE', $str]], $single);

    }


    public function lookupUsersByEmail($email, $single, $substring, $fields)
    {
        if ($substring) {
            $operator = 'CONTAIN';
        } else {
            $operator = 'EQUAL';
        }
        return $this->lookupUser([['Email', $operator, $email]], $single);

    }


    public function lookupUsersByName($name, $single, $substring, $prefname, $fields)
    {
        if (!$prefname) {
            if ($substring) {
                $names = explode(" ", $name);
                if (count($names) < 2) {
                    $result = $this->lookupUser([['First Name', 'CONTAIN', $name]], $single);
                    $result2 = $this->lookupUser([['Last Name', 'CONTAIN', $name]], $single);
                    return $this->mergeResults($result, $result2);
                } else {
                    return $this->lookupUser(
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
                return $this->lookupUser(
                    [['First Name', 'EQUAL', $names[0]],
                                     ['Last Name', 'EQUAL', $names[1]]],
                    $single
                );
            }
        } else {
            $preferredFirstName = search_definedFields('Preferred First Name');
            $preferredLastName = search_definedFields('Preferred Last Name');
            if ($substring) {
                $names = explode(" ", $name);
                if (count($names) < 2) {
                    $result = $this->lookupUser([[$preferredFirstName, 'CONTAIN', $name]], $single);
                    $result2 = $this->lookupUser([[$preferredLastName, 'CONTAIN', $name]], $single);
                    return $this->mergeResults($result, $result2);
                } else {
                    $result = $this->lookupUser(
                        [[$preferredFirstName, 'EQUAL', $names[0]],
                         [$preferredLastName, 'CONTAIN', $names[1]]
                        ],
                        $single
                    );
                    $result2 = $this->lookupUser(
                        [['First Name', 'EQUAL', $names[0]],
                         [$preferredLastName, 'CONTAIN', $names[1]]
                        ],
                        $single
                    );
                    $result = $this->mergeResults($result, $result2);
                    $result2 = $this->lookupUser(
                        [['First Name', 'EQUAL', $names[0]],
                         ['Last Name', 'CONTAIN', $names[1]]
                        ],
                        $single
                    );
                    $result = $this->mergeResults($result, $result2);
                    $result2 = $this->lookupUser(
                        [[$preferredFirstName, 'EQUAL', $names[0]],
                         ['Last Name', 'CONTAIN', $names[1]]
                        ],
                        $single
                    );
                    $result = $this->mergeResults($result, $result2);
                    return $result;
                }
            } else {
                $names = explode(" ", $name);
                if (count($names) != 2) {
                    return ['code' => '400 Bad Request', 'users' => array()];
                }

                /* We have to do a few searchs due to CRM stupidity */
                $output = $this->lookupUser(
                    [[$preferredFirstName, 'EQUAL', $names[0]],
                     [$preferredLastName, 'EQUAL', $names[1]]],
                    $single
                );
                if (count($output['users']) != 0) {
                    return $output;
                }
                $output = $this->lookupUser(
                    [[$preferredFirstName, 'EQUAL', $names[0]],
                     ['Last Name', 'EQUAL', $names[1]]],
                    $single
                );
                if (count($output['users']) != 0) {
                    return $output;
                }
                return $this->lookupUser(
                    [['First Name', 'EQUAL', $names[0]],
                     [$preferredLastName, 'EQUAL', $names[1]]],
                    $single
                );
            }
        }

    }


    public function lookupUsersByNameEmail($first, $last, $email, $fields, $single_user, $substring)
    {
        $criteria = array();
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

        return $this->lookupUser($criteria, $single_user);

    }


    public function lookupUserByLogin($user, $substring, $fields)
    {
        if ($substring) {
            return $this->lookupUser([['Account Login Name', 'CONTAIN', $user]], false);
        } else {
            return $this->lookupUser([['Account Login Name', 'EQUAL', $user]], false);
        }

    }


    public function updateAccount($updateData)
    {
        global $_SESSION;

        $updateData['contactId'] = $this->getContactId($updateData['accountId']);

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

        $preferredFirstName = $this->searchDefinedFields('Preferred First Name');
        $preferredLastName = $this->searchDefinedFields('Preferred Last Name');
        $cvgConComPhoneDisplay = $this->searchDefinedFields('CVGConCom Publish Phone');

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

        return $this->neon->go1($request);

    }


    public function importPeople()
    {
        require($BASEDIR.'/tools/sync_neon_event_to_db.php');

    }


    /*.
    forward private function lookupEvents($page, $output, $all);
    .*/
    private function lookupEvents($page = 1, $output = null, $all = true)
    {
        $search = [
        'method' => 'event/listEvents',
        'columns' => [
        'standardFields' => ['Event Name', 'Event ID', 'Event End Date', 'Event Start Date'],
           ],
        'page' => [
        'currentPage' => $page,
        'pageSize' => 200,
          ],
        ];
        $results = $this->neon->search($search);

        if (isset($results['operationResult']) && $results['operationResult'] == 'SUCCESS') {
            if ($output === null) {
                $output = ['code' => null, 'events' => array()];
            }
            foreach ($results['searchResults'] as $val) {
                array_push($output['events'], $val);
            }
            if ($all && $results['page']['totalPage'] > $page) {
                return $this->lookupEvents($page + 1, $output);
            } else {
                return $output;
            }
        }
        return array('code' => '404 Not Found', 'users' => array());

    }


    private function importBadges($event)
    {
        $search = [
        'method' => 'event/listEventTickets',
        'parameters' => [
        'eventId' => $event,
        'page.currentPage' => 1,
        'page.pageSize' => 200,
          ],
        ];
        $results = $this->neon->go($search);

        $tickets = [];

        if (isset($results['operationResult']) && $results['operationResult'] == 'SUCCESS') {
            foreach ($results['eventTickets']['eventTicket'] as $ticket) {
                $id = $ticket['ticketId'];
                $name = $ticket['ticketName'];
                $cost = $ticket['fee'];
                $sql = "SELECT BadgeTypeID FROM `BadgeTypes` WHERE BadgeTypeID = $id;";
                $result = \DB::run($sql);
                $values = $result->fetch();
                if ($values === false) {
                    $sql = "INSERT INTO `BadgeTypes` (BadgeTypeID, EventID, Name, Cost, AvailableFrom, AvailableTo) VALUES ($id, $event, '$name', $cost, '0000-00-00', '0000-00-00');";
                    \DB::run($sql);
                }
            }
        }

    }


    private function getCycleId($eventdate)
    {
        $year = $eventdate->format("Y-m-d");
        $sql = "SELECT AnnualCycleID FROM AnnualCycles WHERE DateTo > '$year';";
        $result = \DB::run($sql);
        $value = $result->fetch();
        if ($value) {
            $_currentYear = $value['AnnualCycleID'];
            return $_currentYear;
        }
        return null;

    }


    private function neonImportEvents()
    {
        $events = $this->lookupEvents(1, null, true);
        if ($events) {
            // Drop Events we do not care about.
            foreach ($events['events'] as $key => $evt) {
                if (strpos($evt['Event Name'], 'CONvergence') === false) {
                    unset($events['events'][$key]);
                    continue;
                }
                if (strpos($evt['Event Name'], 'Dealers') !== false) {
                    unset($events['events'][$key]);
                    continue;
                }
                if (strpos($evt['Event Name'], 'Example') !== false) {
                    unset($events['events'][$key]);
                    continue;
                }

                $name = $evt['Event Name'];
                $eventenddate = new \DateTime($evt['Event End Date']);
                $eventstartdate = new \DateTime($evt['Event Start Date']);
                $cycle = $this->getCycleId($eventenddate);
                $from = $eventstartdate->format("Y-m-d");
                $to = $eventenddate->format("Y-m-d");
                $evt['AnnualCycleID'] = $cycle;
                $id = $evt['Event ID'];
                $sql = "SELECT EventID FROM `Events` WHERE EventID = $id;";
                $result = \DB::run($sql);
                $values = $result->fetch();
                if ($values === false) {
                    $sql = "INSERT INTO `Events` (EventID, EventName, AnnualCycleID, DateFrom, DateTo) VALUES ($id, '$name', $cycle, '$from', '$to');";
                    \DB::run($sql);
                    $sql = "SELECT EventID FROM `Events` ORDER BY EventID DESC LIMIT 1;";
                    $result = \DB::run($sql);
                    $value = $result->fetch();
                    $eventID = $value['EventID'];
                } else {
                    $eventID = $values['EventID'];
                }
                $this->importBadges($eventID);
                $events['events'][$key]['MySQLEventID'] = $eventID;
            }
        }

        return $events['events'];

    }


    public function importEvents()
    {
        $this->neonImportEvents();

    }


    private function getEventFromDate($eventdate)
    {
        $date = new \DateTime($eventdate);
        $cycle = $this->getCycleId($date);
        $sql = "SELECT EventID FROM Events WHERE AnnualCycleID = '$cycle';";
        $result = \load_table($sql);
        if ($result) {
            $event = $result[0]['EventID'];
            return $event;
        }
        return null;

    }


    public function importMeetings()
    {
        foreach ($_SESSION['definedFields']['customField'] as $field) {
            if ($field['fieldName'] == 'Official Meeting Attendance') {
                foreach ($field['fieldOptions']['fieldOption'] as $meet) {
                    $data = explode(' - ', $meet['name']);


                    $id = $meet['id'];
                    $date = $data[0];
                    $name = $data[1];
                    $event = $this->getEventFromDate($date);
                    $sql = <<<SQL
        INSERT IGNORE INTO `OfficialMeetings`
        SET  MeetingID = $id,
        Date = '$date',
        EventID = $event,
        Name = '$name';
SQL;
                    \DB::run($sql);
                }
            }
        }

    }


    private function neonCreateWebhook($url, $name, $type)
    {
            // Custom Field Data for Session - Parse it to an easy to use array
        $request = [
        'method' => 'webhook/createWebhook',
        'parameters' => [
        'webhook.webhookName' => "${name
        }_${type
    }",
        'webhook.notifyUrl' => "$url/neon_webhook.php",
        'webhook.eventTrigger' => $type,
        'webhook.contentType' => 'application/json'
        ],
        ];
        $result = $this->neon->go($request);
        if (isset($result['operationResult']) && $result['operationResult'] == 'SUCCESS') {
            $hookname = substr("NH_ID_$type", 0, 14);
            \set_conf_value($hookname, $result['id']);
            return null;
        }
        return $result;

    }


    public function registerWebhook($url, $name)
    {
        $this->neonCreateWebhook($url, $name, 'CREATE_ACCOUNT');
        $this->neonCreateWebhook($url, $name, 'UPDATE_ACCOUNT');
        \set_conf_value('NEON_HOOK_NAME', $name);
        \set_conf_value('NEON_HOOK_URL', $url);

    }


    private function neonRemoveWebhook($type)
    {
        $hookname = substr("NH_ID_$type", 0, 14);
    // Custom Field Data for Session - Parse it to an easy to use array
        $request = [
        'method' => 'webhook/deleteWebhook',
        'parameters' => [
        'id' => "${GLOBALS[$hookname]
}"
        ],
        ];
        $result = $this->neon->go($request);
        if (isset($result['operationResult']) && $result['operationResult'] == 'SUCCESS') {
            \del_conf_value($hookname);
            return null;
        }
        return $result;

    }


    public function removeWebhook()
    {
        $this->neonRemoveWebhook('CREATE_ACCOUNT');
        $this->neonRemoveWebhook('UPDATE_ACCOUNT');
        \del_conf_value('NEON_HOOK_NAME');
        \del_conf_value('NEON_HOOK_URL');

    }


    private function updateDBVersion($target)
    {
        $sql = <<<SQL
UPDATE Configuration
SET Value = '$target'
WHERE Field = 'DBSchemaVersion';
SQL;
        \DB::run($sql);
        echo "<br><hr>";
        echo "<h3>Database Schema updated to: $target</h3>";
        echo "<h3>Done!</h3>";

    }


    public function update($from, $to)
    {
        if ($from < 2018011700) {
            require_once(__DIR__."/neon/from_neon_to_152.inc");
            from_neon_to_152($this->neon);
            $this->updateDBVersion(2018011700);
        }
        if ($from < 2018091700) {
            require_once(__DIR__."/neon/to_180501.inc");
            to_180501($this->neon);
            $this->updateDBVersion(2018091700);
        }
        if ($from < 2018092400) {
            require_once(__DIR__."/neon/to_180924.inc");
            to_180924($this->neon);
            $this->updateDBVersion(2018092400);
        }

    }


/* end class */
}
