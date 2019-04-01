<?php

/*.
    require_module 'standard';
.*/

require_once(__DIR__."/../functions.inc");
require_once(__DIR__."/../database.inc");


function _ConComListBuildSearch($page)
{
    global $Neon;

    $conComFieldId = search_definedFields('CVGConCom Current Position');
    $preferredFirstName = search_definedFields('Preferred First Name');
    $preferredLastName = search_definedFields('Preferred Last Name');

    $search = [
    'method' => 'account/listAccounts',
    'criteria' => [
    [$conComFieldId, 'NOT_BLANK', ''],
    ],
    'columns' => [
    'standardFields' => ['Account ID', 'First Name', 'Last Name', 'Email 1'],
    'customFields' => [$conComFieldId, $preferredFirstName, $preferredLastName],
    ],
    'page' => [
    'currentPage' => $page,
    'pageSize' => 200,
    'sortColumn' => 'Account ID',
    'sortDirection' => 'ASC',
    ],
    ];
    $result = $Neon->search($search);

    if (isset($result['operationResult']) && $result['operationResult'] == 'SUCCESS') {
        $data['pages'] = $result['page']['totalPage'];
        foreach ($result['searchResults'] as $val) {
            $positionsHeld = explode("|", $val['CVGConCom Current Position']);
            foreach ($positionsHeld as $pos) {
                $tmp = explode(",", $pos);
                $line = [
                'Account ID' => $val['Account ID'],
                'Department' => $tmp[0],
                'Position'   => $tmp[1],
                'Email'      => $val['Email 1'],
                ];

                if (empty($val['Preferred First Name'])) {
                    $line['First Name'] = $val['First Name'];
                } else {
                    $line['First Name'] = $val['Preferred First Name'];
                }

                if (empty($val['Preferred Last Name'])) {
                    $line['Last Name'] = $val['Last Name'];
                } else {
                    $line['Last Name'] = $val['Preferred Last Name'];
                }

                if (isset($tmp[2])) {
                    $line['Note'] = $tmp[2];
                } else {
                    $line['Note'] = "";
                }

                $data['list'][] = $line;
            }
            unset($tmp);
            unset($line);
            unset($pos);
            unset($positionsHeld);
        }
        unset($val);
    } else {
        print "Could not retrieve ConComList ".__FILE__." : ";
        print "<pre>\n";
        print_r($result);
        print "</pre>\n";
    }
    return $data;

}


function _ConComListBuild()
{
    $fullConComList[] = [];
    $i = 1;
    $runPage = 1;
    while ($i <= $runPage) {
        $result = _ConComListBuildSearch($i);
        $tmp = array_merge_recursive($fullConComList, $result['list']);
        $fullConComList = $tmp;
        unset($tmp);
        $runPage = $result['pages'];
        $i++;
    }

    return($fullConComList);

}


function _build_ConComList()
{
    $event = current_eventID();

    $data = _ConComListBuild();
    print '<div id="update_staff" class="w3-container">';
    foreach ($data as $line) {
        if (!empty($line['Account ID'])) {
            if ($line['Position'] == 'Director') {
                $id = 1;
            } else {
                $sql = "SELECT COALESCE(PositionID, 0) as PositionID FROM ConComPositions WHERE Name = '".$line['Position']."';";
                $result = DB::run($sql);
                $value = $result->fetch();
                if ($value) {
                    $id = $value['PositionID'];
                } else {
                    print '<h2 style="color:red"> Record for '.$line['First Name'].' '.$line['Last Name'].'('.$line['Account ID'].') as '.$line['Department'].' '.$line['Position'].', Unknown position: '.$line['Position'].'</h2><br>';
                    $id = 0;
                }
            }

            $sql = "SELECT DepartmentID FROM Departments WHERE Name = '".$line['Department']."';";
            $result = DB::run($sql);
            $value = $result->fetch();
            if ($value) {
                $did = $value['DepartmentID'];
            } else {
                print '<h2 style="color:red"> Record for '.$line['First Name'].' '.$line['Last Name'].'('.$line['Account ID'].') as '.$line['Department'].' '.$line['Position'].', Unknown department: '.$line['Department'].'</h2><br>';
                $did = 0;
            }

            /* Do we have this record already? If so then DB is god */
            $sql = "SELECT AccountID FROM ConComList WHERE AccountID = ".$line['Account ID']." AND DepartmentID = ".$did." AND PositionID = ".$id.";";
            $result = DB::run($sql);
            $value = $result->fetch();
            if ($value) {
                print '<span>Record for '.$line['First Name'].' '.$line['Last Name'].'('.$line['Account ID'].') as '.$line['Department'].' '.$line['Position'].' already exists. Skip Import from Neon.</span><br>';
                continue;
            }

            if ($line['Note']) {
                $sql = "INSERT INTO ConComList (AccountID, DepartmentID, PositionID, EventID, Note) VALUES (".$line['Account ID'].", ".$did.", ".$id.", $event, '".$line['Note']."');";
            } else {
                $sql = "INSERT INTO ConComList (AccountID, DepartmentID, PositionID, EventID) VALUES (".$line['Account ID'].", ".$did.", ".$id.", $event);";
            }
            DB::run($sql);
            print '<span style="color:green">Imported '.$line['First Name'].' '.$line['Last Name'].'('.$line['Account ID'].') as '.$line['Department'].' '.$line['Position'].' from Neon.</span><br>';
        }
    }
    print '</div>';

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


function _Neon_import_badges($event)
{
    global $Neon;

    $search = [
    'method' => 'event/listEventTickets',
    'parameters' => [
    'eventId' => $event,
    'page.currentPage' => 1,
    'page.pageSize' => 200,
      ],
    ];
    $results = $Neon->go($search);

    $tickets = [];

    if (isset($results['operationResult']) && $results['operationResult'] == 'SUCCESS') {
        foreach ($results['eventTickets']['eventTicket'] as $ticket) {
            $id = $ticket['ticketId'];
            $name = $ticket['ticketName'];
            $cost = $ticket['fee'];
            $sql = "SELECT BadgeTypeID FROM `BadgeTypes` WHERE BadgeTypeID = $id;";
            $result = DB::run($sql);
            $values = $result->fetch();
            if ($values === false) {
                $sql = "INSERT INTO `BadgeTypes` (BadgeTypeID, EventID, Name, Cost, AvailableFrom, AvailableTo) VALUES ($id, $event, '$name', $cost, '0000-00-00', '0000-00-00');";
                DB::run($sql);
            }
        }
    }

}


function _get_cycleID($eventdate)
{
    $year = $eventdate->format("Y-m-d");
    $sql = "SELECT AnnualCycleID FROM AnnualCycles WHERE DateTo > '$year';";
    $result = DB::run($sql);
    $value = $result->fetch();
    if ($value) {
        $_currentYear = $value['AnnualCycleID'];
        return $_currentYear;
    }
    return null;

}


function _Neon_import_events()
{
    $events = lookup_events(1, null, true);
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
            $eventenddate = new DateTime($evt['Event End Date']);
            $eventstartdate = new DateTime($evt['Event Start Date']);
            $cycle = _get_cycleID($eventenddate);
            $from = $eventstartdate->format("Y-m-d");
            $to = $eventenddate->format("Y-m-d");
            $evt['AnnualCycleID'] = $cycle;
            $id = $evt['Event ID'];
            $sql = "SELECT EventID FROM `Events` WHERE EventID = $id;";
            $result = DB::run($sql);
            $values = $result->fetch();
            if ($values === false) {
                $sql = "INSERT INTO `Events` (EventID, EventName, AnnualCycleID, DateFrom, DateTo) VALUES ($id, '$name', $cycle, '$from', '$to');";
                DB::run($sql);
                $sql = "SELECT EventID FROM `Events` ORDER BY EventID DESC LIMIT 1;";
                $result = DB::run($sql);
                $value = $result->fetch();
                $eventID = $value['EventID'];
            } else {
                $eventID = $values['EventID'];
            }
            _Neon_import_badges($eventID);
            $events['events'][$key]['MySQLEventID'] = $eventID;
        }
    }

    return $events['events'];

}


function from_neon_to_152()
{
    _Neon_import_events();
    _build_ConComList();

}
