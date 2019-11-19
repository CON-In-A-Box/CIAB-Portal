<?php

require_once(__DIR__."/../functions/database.inc");


function maxQuery()
{
    if (!array_key_exists('MAX_PACKET', $GLOBALS)) {
        $sql = "SHOW VARIABLES LIKE 'max_allowed_packet';";
        $result = \DB::run($sql);
        $value = $result->fetch();
        $GLOBALS['MAX_PACKET'] = intval($value['Value']) / 160;
    }

}


function _loadEventCustomFields()
{
    global $Neon;

    $fields = [];
    $fields['definedFields'] = [];

    // Custom Field Data for Session - Parse it to an easy to use array
    $request = [
    'method' => 'common/listCustomFields',
    'parameters' => [
    'searchCriteria.component' => "Event",
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


function _lookup_events_attendees($fields, $event, $page = 1, $output = null, $all = true)
{
    global $Neon;

    echo '.';
    $search = [
    'method' => 'event/retrieveEventAttendees',
    'parameters' => [
    'eventId' => $event,
    'page.currentPage' => $page,
    'page.pageSize' => 50,
      ],
    ];
    $results = $Neon->go($search);

    if (isset($results['operationResult']) && $results['operationResult'] == 'SUCCESS') {
        if ($output === null) {
            $output = ['code' => null, 'attendees' => array()];
        }
        foreach ($results['eventAttendeesResults']['eventAttendeesResult'] as $val) {
            if (isset($val['customFieldDataList'])) {
                foreach ($val['customFieldDataList']['customFieldData'] as $cval) {
                    if (array_key_exists('fieldValue', $cval)) {
                        $val[$fields['definedFields'][$cval['fieldId']]] = $cval['fieldValue'];
                    } elseif (array_key_exists('fieldOptionId', $cval)) {
                        $val[$fields['definedFields'][$cval['fieldId']]] = $cval['fieldOptionId'];
                    }
                }
                unset($val['customFieldDataList']);
            }
            array_push($output['attendees'], $val);
        }
        if ($all && $results['page']['totalPage'] > $page) {
            echo $page.'/'.$results['page']['totalPage']."\n";
            return _lookup_events_attendees($fields, $event, $page + 1, $output);
        } else {
            echo '.';
            return $output;
        }
    }
    return array('code' => '404 Not Found', 'users' => array());

}


function _updateRegistrations($people, $event)
{
    $sql = <<<SQL
        REPLACE INTO `Registrations` (RegistrationID, AccountID, EventID,
            RegisteredByID, RegistrationDate, BadgesPickedUp, BadgeName, BadgeTypeID,
            EmergencyContact)
        VALUES
SQL;

    $data = [];
    foreach ($people as $person) {
        $key = $person['attendeeId'];
        if (array_key_exists('attendeeAccountId', $person)) {
            $accountID = $person['registrantAccountId'];
        } else {
            $accountID = $person['attendeeId'];
        }
        $eventID = $event['MySQLEventID'];
        $regByID = $person['registrantAccountId'];
        $dt = new DateTime($person['registrationDate']);
        $date = MyPDO::quote($dt->format("Y-m-d H:i:s"));
        if (array_key_exists('Number of Active Badges', $person)) {
            $pickup = $person['Number of Active Badges'];
        } else {
            $pickup = 'NULL';
        }
        if (array_key_exists('Badge Name', $person)) {
            $badge = MyPDO::quote(substr($person['Badge Name'], 0, 100));
        } else {
            $badge = 'NULL';
        }
        /* TODO: BadgeTypeID */
        $typeID = 1;
        /* TODO: BadgeDependentOnID */
        if (array_key_exists('In Case Of Emergency (Name and Phone)', $person)) {
            $contact = MyPDO::quote($person['In Case Of Emergency (Name and Phone)']);
        } else {
            $contact = 'NULL';
        }
        $data[] = "($key, $accountID, $eventID, $regByID, $date, $pickup, $badge, $typeID, $contact)";
    }
    $sql .= implode(", ", $data);
    $sql .= ';';

    DB::run($sql);

}


function _import_page_of_people($event, $people, $page)
{
    $total = 0;
    $eventID = $event['MySQLEventID'];
    echo '.';
    _updateRegistrations($people['attendees'], $event);
    echo '.';
    $total += count($people['attendees']);
    return $total;

}
