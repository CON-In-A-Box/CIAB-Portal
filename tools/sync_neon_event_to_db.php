<?php

/*.
    require_module 'standard';
.*/

require_once(__DIR__."/../functions/functions.inc");
require_once(__DIR__."/../functions/database.inc");


function _loadEventCustomFields()
{
    global $Neon;

    // Custom Field Data for Session - Parse it to an easy to use array
    $request = [
    'method' => 'common/listCustomFields',
    'parameters' => [
    'searchCriteria.component' => "Event",
        ],
        ];
    $result = $Neon->go($request);
    if (isset($result['operationResult']) && $result['operationResult'] == 'SUCCESS') {
        $_SESSION['definedFields']['customField'] = $result['customFields']['customField'];
        foreach ($result['customFields']['customField'] as $val) {
            $_SESSION['definedFields'][$val['fieldId']] = $val['fieldName'];
        }
    } else {
        die("Impossible error during Defined Custom Fields Download");
    }

}


function _lookup_events_attendees($event, $page = 1, $output = null, $all = true)
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
                        $val[$_SESSION['definedFields'][$cval['fieldId']]] = $cval['fieldValue'];
                    } elseif (array_key_exists('fieldOptionId', $cval)) {
                        $val[$_SESSION['definedFields'][$cval['fieldId']]] = $cval['fieldOptionId'];
                    }
                }
                unset($val['customFieldDataList']);
            }
            array_push($output['attendees'], $val);
        }
        if ($all && $results['page']['totalPage'] > $page) {
            echo $page.'/'.$results['page']['totalPage']."\n";
            return _lookup_events_attendees($event, $page + 1, $output);
        } else {
            return $output;
        }
    }
    return array('code' => '404 Not Found', 'users' => array());

}


function lookup_events_attendees($event, $page = 1, $all = true)
{
    return _lookup_events_attendees($event, $page, null, $all);

}


function _updateMember($person, $event, $add)
{
    global $db;

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
    $db->run($sql);

}


function _import_page_of_people($event, $people, $page)
{
    global $db;

    $total = 0;
    $eventID = $event['MySQLEventID'];
    foreach ($people['attendees'] as $person) {
        $key = $person['attendeeId'];
        $sql = <<<SQL
            SELECT RegistrationID
            FROM `Registrations`
            WHERE EventID = $eventID AND RegistrationID = $key;
SQL;
        $result = $db->run($sql);
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
        $db->run($sql);
    }
    return $total;

}


function _Neon_import_people($event)
{
    /* When really ready switch to true */
    $page = 1;
    $total = 0;
    $round = 0;
    print "Event: ".$event['Event ID']."\n";
    do {
        $people = lookup_events_attendees($event['Event ID'], $page, false);
        $count = count($people['attendees']);
        if ($count) {
            $round = _import_page_of_people($event, $people, $page);
            $total += $round;
        } else {
            break;
        }
        print $page.": ".count($people['attendees'])." ".$total."\n";
        if ($round != $count) {
            print "Person count missmatch. Neon has ".$count." people and we ";
            print "imported ".$total." people.";
        }
        $page++;
    } while (true);
    print "done\n";

}


function lookup_events($page = 1, $output = null, $all = true)
{
    global $Neon;

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
    $results = $Neon->search($search);

    if (isset($results['operationResult']) && $results['operationResult'] == 'SUCCESS') {
        if ($output === null) {
            $output = ['code' => null, 'events' => array()];
        }
        foreach ($results['searchResults'] as $val) {
            array_push($output['events'], $val);
        }
        if ($all && $results['page']['totalPage'] > $page) {
            return lookup_events($page + 1, $output);
        } else {
            return $output;
        }
    }
    return array('code' => '404 Not Found', 'users' => array());

}


function _Neon_events()
{
    global $db;

    $events = lookup_events();
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

            $events['events'][$key]['MySQLEventID'] = $evt['Event ID'];
        }
    }

    return $events['events'];

}


function do_Neon_import()
{
    _loadEventCustomFields();
    $events = _Neon_events();
    $event = array_slice($events, -1)[0];
    _Neon_import_people($event);

}


$cmd = "pgrep -f ".basename(__FILE__);
exec($cmd, $pids);
if (!empty($pids)) {
    print "Sync already in progress";
    exit();
}


do_Neon_import();
