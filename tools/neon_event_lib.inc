<?php

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

    $search = [
    'method' => 'event/retrieveEventAttendees',
    'parameters' => [
    'eventId' => $event,
    'page.currentPage' => $page,
    'page.pageSize' => 20,
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
            return $output;
        }
    }
    return array('code' => '404 Not Found', 'users' => array());

}


function _updateMember($person, $event, $add)
{
    $key = $person['attendeeId'];
    $accountID = $person['attendeeAccountId'];
    $eventID = $event['MySQLEventID'];
    $regByID = $person['registrantAccountId'];
    $dt = new DateTime($person['registrationDate']);
    $date = $dt->format("Y-m-d H:i:s");
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

    if ($add) {
        $sql = <<<SQL
            INSERT INTO `Registrations` (RegistrationID, AccountID, EventID,
                RegisteredByID, RegistrationDate, BadgesPickedUp, BadgeName, BadgeTypeID,
                EmergencyContact)
            VALUES ($key, $accountID, $eventID, $regByID, '$date', $pickup, $badge, $typeID, $contact);
SQL;
    } else {
        $sql = <<<SQL
            UPDATE `Registrations`
            SET AccountID = $accountID,
                EventID = $eventID,
                RegisteredByID = $regByID,
                RegistrationDate = '$date',
                BadgesPickedUp = $pickup,
                BadgeName = $badge,
                BadgeTypeID = $typeID,
                EmergencyContact = $contact
            WHERE RegistrationID = $key;
SQL;
    }
    DB::run($sql);

}


function _import_page_of_people($event, $people, $page)
{
    $total = 0;
    $eventID = $event['MySQLEventID'];
    foreach ($people['attendees'] as $person) {
        $key = $person['attendeeId'];
        $sql = <<<SQL
            SELECT RegistrationID
            FROM `Registrations`
            WHERE EventID = $eventID AND RegistrationID = $key;
SQL;
        $result = DB::run($sql);
        $value = $result->fetch();
        if ($value !== false) {
            _updateMember($person, $event, false);
        } else {
            _updateMember($person, $event, true);
        }
        $total ++;

        $accountID = $person['attendeeAccountId'];
        $sql = <<<SQL
            INSERT IGNORE INTO `TempEventPage`
            SET  AccountID = $accountID,
                 PageFound = $page;
SQL;
        DB::run($sql);
    }
    return $total;

}
