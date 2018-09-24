<?php

require_once($FUNCTIONDIR."/database.inc");
require_once($FUNCTIONDIR."/update/from_neon_to_152.inc");


function list_events()
{
    $sql = <<<SQL
        SELECT
            e.`EventID`,
            e.`DateFrom`,
            e.`DateTo`,
            e.`EventName`,
            a.`DateFrom` as CycleDateFrom,
            a.`DateTo` as CycleDateTo
        FROM
            `Events` as e
            INNER JOIN AnnualCycles as a ON e.`AnnualCycleID` = a.`AnnualCycleID`
        ORDER BY
            e.`DateFrom` DESC
SQL;

    $values = [
    'Id' => 'EventID',
    'From' => 'DateFrom',
    'To' => 'DateTo',
    'Name' => 'EventName',
    'CycleFrom' => 'CycleDateFrom',
    'CycleTo' => 'CycleDateTo',
    ];
    $data = load_table($sql, $values);
    $keys = array_keys($values);
    print "<tr><th>".implode('</th><th>', $keys)."</th></tr>\n";
    foreach ($data as $entry) {
        print "<tr><td>".implode('</td><td>', $entry)."</td></tr>\n";
    }

}


function list_meetings()
{
    $sql = <<<SQL
    SELECT
        m.`MeetingID`,
        m.`Date`,
        m.`Name`,
        e.`EventName`,
        e.`EventID`
    FROM
        `OfficialMeetings` as m
        INNER JOIN `Events` as e ON m.`EventID` = e.`EventID`
    ORDER BY
        e.`EventName` DESC,
        e.`DateFrom` DESC,
        m.`Date` DESC
SQL;

    $values = [
    'Id' => 'MeetingID',
    'Date' => 'Date',
    'Name' => 'Name',
    'Event' => 'EventName',
    'EventID' => 'EventID',
    ];
    $data = load_table($sql, $values);
    $keys = array_keys($values);
    print "<tr><th>".implode('</th><th>', $keys)."</th><th></th></tr>\n";
    foreach ($data as $entry) {
        $json = json_encode($entry);
        $json = base64_encode($json);
        print "<tr><td>".implode('</td><td>', $entry);
        print "<td> <button class='w3-button w3-round-xxlarge event-color-primary' onclick='editMeeting(\"$json\")'>Modify Meeting</button> </td>";
        print "</tr>\n";
    }

}


function _get_event_from_Date($eventdate)
{
    $date = new DateTime($eventdate);
    $cycle = _get_cycleID($date);
    $sql = "SELECT EventID FROM Events WHERE AnnualCycleID = '$cycle';";
    $result = load_table($sql);
    if ($result) {
        $event = $result[0]['EventID'];
        return $event;
    }
    return null;

}


function import_meetings()
{
    foreach ($_SESSION['definedFields']['customField'] as $field) {
        if ($field['fieldName'] == 'Official Meeting Attendance') {
            foreach ($field['fieldOptions']['fieldOption'] as $meet) {
                $data = explode(' - ', $meet['name']);
                $id = $meet['id'];
                $date = $data[0];
                $name = $data[1];
                $event = _get_event_from_Date($date);
                $sql = <<<SQL
                    INSERT IGNORE INTO `OfficialMeetings`
                    SET  MeetingID = $id,
                         Date = '$date',
                         EventID = $event,
                         Name = '$name';
SQL;
                DB::run($sql);
            }
        }
    }

}


function import_events()
{
    _Neon_import_events();

}


function select_events()
{
    $sql = <<<SQL
        SELECT
            `EventID`,
            `EventName`
        FROM
            `Events`
        ORDER BY
            `DateFrom` DESC
SQL;

    $values = [
    'Id' => 'EventID',
    'Name' => 'EventName',
    ];
    $data = load_table($sql, $values);
    return $data;

}


function save_meeting($meeting)
{
    $date = $meeting->Date;
    $name = $meeting->Name;
    $event = $meeting->EventID;
    if ($meeting->Id == -1) {
        $sql = <<<SQL
    INSERT INTO `OfficialMeetings` (
        `MeetingID`, `Date`, `EventID`, `Name`
    )
    VALUES
        (
            NULL, '$date', $event, '$name'
        )
SQL;
    } else {
        $id = $meeting->Id;
        $sql = <<<SQL
    UPDATE
        `OfficialMeetings`
    SET
        `Date` = '$date',
        `EventID` = $event,
        `Name` = '$name'
    WHERE
        `MeetingID` = $id
SQL;
    }
    DB::run($sql);

}
