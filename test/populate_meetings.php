<?php

require_once(__DIR__."/../functions/functions.inc");
require_once(__DIR__."/../functions/database.inc");


function random_concom_ids()
{
    $sql = <<<SQL
        SELECT AccountID FROM ConComList
        ORDER BY RAND()
        LIMIT 20
SQL;
    $result = DB::run($sql);
    return $result->fetchAll();

}


function create_meetings($count)
{
    $event = \current_eventID();
    $cycle = \current_cycleID();
    $sql = "SELECT DateFrom, DateTo FROM AnnualCycles WHERE AnnualCycleID = $cycle";
    $result  = DB::run($sql);
    $value = $result->fetch();

    $start = strtotime($value['DateFrom']);
    $stop = strtotime($value['DateTo']);
    $interval = ($stop - $start) / $count;

    for ($i = 1; $i <= $count; $i++) {
        $start += $interval;
        $sql = "INSERT INTO `OfficialMeetings` (Date, EventID, Name) VALUES ('";
        $sql .= date("Y-m-d", $start);
        $sql .= "', $event, 'Meeting $i');";
        $result  = DB::run($sql);
    }

}


function populate_attendance()
{
    $sql = "SELECT MeetingID FROM `OfficialMeetings` WHERE 1";
    $result = DB::run($sql);
    $value = $result->fetch();
    while ($value != false) {
        $members = random_concom_ids();
        foreach ($members as $member) {
            $sql = "INSERT INTO `MeetingAttendance` (AccountID, MeetingID) VALUES (";
            $sql .= "${member['AccountID']}, ${value['MeetingID']});";
            DB::run($sql);
        }
        $value = $result->fetch();
    }
    print("done");

}


function populate_meetings()
{
    print "Purge\n";

    $sql = "DELETE FROM `MeetingAttendance` WHERE 1";
    DB::run($sql);
    $sql = "DELETE FROM `OfficialMeetings` WHERE 1";
    DB::run($sql);

    create_meetings(13);
    populate_attendance();

}


populate_meetings();
print "Complete\n";
