<?php

/*.
    require_module 'standard';
.*/

require_once(__DIR__."/../functions/functions.inc");
require_once(__DIR__."/neon_event_lib.inc");


function _Neon_import_people($fields, $event)
{
    /* When really ready switch to true */
    $page = 1;
    $total = 0;
    $round = 0;
    print "Event: ".$event['Event ID']."\n";
    do {
        $people = _lookup_events_attendees(
            $fields,
            $event['Event ID'],
            $page,
            null,
            false
        );
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


function do_event_Neon_import($history)
{
    maxQuery();
    $fields = _loadEventCustomFields();
    $events = _Neon_events();
    if (!$history) {
        $event = array_slice($events, -1)[0];
        _Neon_import_people($fields, $event);
    } else {
        foreach ($events as $event) {
            echo $event['Event Name']."\n";
            _Neon_import_people($fields, $event);
        }
    }

}


$cmd = "pgrep -f ".basename(__FILE__);
exec($cmd, $pids);
if (!empty($pids)) {
    if (count($pids) > 1 || $pids[0] != getmypid()) {
        print "Sync already in progress";
        exit();
    }
}

$historical = false;

for ($i = 0; $i < count($argv); ++$i) {
    if ($argv[$i] === '--historical') {
        $historical = true;
    }
}

do_event_Neon_import($historical);
