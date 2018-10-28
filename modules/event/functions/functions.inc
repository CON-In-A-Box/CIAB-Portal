<?php

/*.
    require_module 'standard';
    require_module 'json';
.*/

require_once($FUNCTIONDIR."/database.inc");
require_once($FUNCTIONDIR."/update/from_neon_to_152.inc");


function _event_details($id)
{
    $sql = <<<SQL
    SELECT
        (
            SELECT
                COUNT(b.`BadgeTypeID`)
            FROM
                `BadgeTypes` as b
            WHERE
                b.`EventID` = e.`EventID`
        ) as badges_types,
        (
            SELECT
                COUNT(
                    DISTINCT(c.`AccountID`)
                )
            FROM
                `ConComList` as c
            WHERE
                c.`EventID` = e.`EventID`
        ) as concom,
        (
            SELECT
                COUNT(h.`ClaimID`)
            FROM
                `HourRedemptions` as h
            WHERE
                h.`EventID` = e.`EventID`
        ) as prizes_claimed,
        (
            SELECT
                COUNT(m.`MeetingID`)
            FROM
                `OfficialMeetings` as m
            WHERE
                m.`EventID` = e.`EventID`
        ) as meetings,
        (
            SELECT
                COUNT(r.`RegistrationID`)
            FROM
                `Registrations` as r
            WHERE
                r.`EventID` = e.`EventID`
        ) as registrations,
        (
            SELECT
                SUM(v.`ActualHours` * v.`TimeModifier`)
            FROM
                `VolunteerHours` as v
            WHERE
                v.`EventID` = e.`EventID`
        ) as volunteer_hours
    FROM
        Events AS e
    WHERE
        EventID = $id;
SQL;
    $data = load_table($sql);
    return $data[0];

}


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
    $event_data = load_table($sql, $values);
    $event_keys = array_keys($values);

    $sql = <<<SQL
        SELECT
            BadgeTypeID,
            Name,
            Cost,
            EventID,
            (
                SELECT
                    COUNT(`RegistrationID`)
                FROM
                    `Registrations` as r
                WHERE
                    r.`BadgeTypeID` = b.`BadgeTypeID`
            ) as Sold,
            AvailableFrom,
            AvailableTo
        FROM
            `BadgeTypes` as b
SQL;
    $values = [
    'Id' => 'BadgeTypeID',
    'Badge Name' => 'Name',
    'Cost' => 'Cost',
    'Event' => 'EventID',
    'Sold' => 'Sold',
    'From' => 'AvailableFrom',
    'To' => 'AvailableTo',
    ];
    $badge_data = load_table($sql, $values);
    $badge_keys = array_keys($values);

    print "<tr><th>".implode('</th><th>', $event_keys)."</th></tr>\n";
    foreach ($event_data as $entry) {
        $plain_name = htmlspecialchars($entry['Name'], ENT_QUOTES);
        $entry['Name'] .= " <i id='badges_".$entry['Id']."_arrow' class='fa fa-caret-down'>";
        print "<tr class='event-hover-primary' ";
        print "onclick='expandEvent(\"badges_".$entry['Id']."\")'>";
        print "<td>".implode('</td><td>', $entry)."</td>";
        print "</tr>\n";

        print "<tr><td colspan=5><div name='badges_".$entry['Id']."' class=' w3-hide'><table class='w3-table-all w3-center'>";

        print "<tr>";
        unset($badge_keys[0]);
        unset($badge_keys[3]);
        unset($badge_keys[5]);
        unset($badge_keys[6]);
        print "<th>".implode('</th><th>', $badge_keys)."</th><th></th>";
        print "</tr>\n";
        foreach ($badge_data as $badge) {
            if ($badge['Event'] == $entry['Id']) {
                $badge['EventName'] = $plain_name;
                $json = json_encode($badge);
                $json = base64_encode($json);
                $id  = $badge['Id'];
                unset($badge['Id']);
                unset($badge['Event']);
                unset($badge['From']);
                unset($badge['To']);
                unset($badge['EventName']);
                print "<tr> ";
                print "<td>".implode('</td><td>', $badge)."</td>";
                print "<td class='w3-center'>";
                print "<button class='w3-button w3-small w3-round-xxlarge w3-green'  onclick='editBadge(\"$json\");'>Edit</button>  ";
                if (intval($badge['Sold']) == 0) {
                    $name = $badge['Badge Name'];
                    print "<button class='w3-button w3-small w3-round-xxlarge w3-red' onclick='deleteBadge($id,\"$name\");'>Delete</button>";
                } else {
                    print "<button class='w3-button w3-small w3-round-xxlarge w3-red' disabled>Delete</button>";
                }
                print "</td>";
                print "</tr>\n";
            }
        }
        print "<tr> <td colspan=4 class='w3-center'>";
        $event = $entry['Id'];
        print "<button id='new' class='w3-button w3-round-xxlarge event-color-primary' onclick='newBadge($event, \"$plain_name\")'>Add New Badge</button>";
        print "</td></tr>";
        print "</table>";
        print "</div></td>";
        print "<td> <div name='badges_".$entry['Id']."' class=' w3-hide'>";

        $details = _event_details($entry['Id']);
        $can_delete = true;
        print "<table class='w3-table-all'>";
        foreach ($details as $key => $detail) {
            print "<tr><td>".$key."</td><td>";
            print $detail."</td></tr>";
            if ($can_delete && intval($detail) > 0) {
                $can_delete = false;
            }
        }
        print "</table>";
        print "<div class='w3-center'>";
        $json = json_encode($entry);
        $json = base64_encode($json);
        print "<button class='w3-button w3-round-xxlarge event-color-primary w3-margin' onclick='editEvent(\"$plain_name\",\"$json\")'>Modify Event</button>";
        print "</div>";
        print "<div class='w3-center'>";
        if ($can_delete) {
            print "<button class='w3-button w3-round-xxlarge w3-red' onclick='deleteEvent(".$entry['Id'].",\"$plain_name\")'>Delete Event</button>";
        } else {
            print "<button class='w3-button w3-round-xxlarge w3-red w3-disabled' >Delete Event</button>";
        }
        print "</div>";
        print "</div></td>";
        print "</tr>";
    }

}


function _check_attendance($id)
{
    $sql = <<<SQL
    SELECT
        COUNT(`AccountID`) AS members
    FROM
        `MeetingAttendance`
    WHERE
        `MeetingID` = $id;
SQL;
    $data = load_table($sql);
    return $data[0]['members'];

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
    print "<tr><th>".implode('</th><th>', $keys)."</th><th></th><th></th></tr>\n";
    foreach ($data as $entry) {
        $json = json_encode($entry);
        $json = base64_encode($json);
        print "<tr><td>".implode('</td><td>', $entry);
        print "<td> <button class='w3-button w3-round-xxlarge event-color-primary' onclick='editMeeting(\"$json\")'>Modify Meeting</button> </td>";
        $attendance = _check_attendance($entry['Id']);
        if ($attendance > 0) {
            print "<td class='w3-center'> <span class='w3-button w3-round-xxlarge w3-red w3-disabled'>".$attendance." Attended </span> </td>";
        } else {
            print "<td class='w3-center'> <button class='w3-button w3-round-xxlarge w3-red' onclick='deleteMeeting(\"".$entry['Name'].'",'.$entry['Id'].")'>Delete Meeting</button> </td>";
        }
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
    $name = MyPDO::quote($meeting->Name);
    $event = $meeting->EventID;
    if ($meeting->Id == -1) {
        $sql = <<<SQL
    INSERT INTO `OfficialMeetings` (
        `MeetingID`, `Date`, `EventID`, `Name`
    )
    VALUES
        (
            NULL, '$date', $event, $name
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
        `Name` = $name
    WHERE
        `MeetingID` = $id
SQL;
    }
    DB::run($sql);

}


function delete_meeting($id)
{
    $sql = "DELETE FROM `OfficialMeetings` WHERE `MeetingID` = $id;";
    DB::run($sql);

}


function list_cycles()
{
    global $last_cycle;

    $sql = <<<SQL
        SELECT
            *
        FROM
            `AnnualCycles`
        ORDER BY
            `DateFrom` DESC
SQL;

    $values = [
    'Id' => 'AnnualCycleID',
    'From' => 'DateFrom',
    'To' => 'DateTo',
    ];
    $data = load_table($sql, $values);
    $keys = array_keys($values);
    print "<tr><th>".implode('</th><th>', $keys)."</th></tr>\n";
    $last_cycle = null;
    foreach ($data as $entry) {
        if ($last_cycle === null) {
            $last_cycle = $entry;
        }
        print "<tr><td>".implode('</td><td>', $entry)."</td></tr>\n";
    }

}


function new_cycle($cycle)
{
    $from = $cycle->From;
    $to = $cycle->To;
    $sql = "INSERT INTO `AnnualCycles` (`AnnualCycleID`, `DateFrom`, `DateTo`) VALUES (NULL, '$from', '$to')";
    DB::run($sql);

}


function delete_badge($id)
{
    $sql = "DELETE FROM `BadgeTypes` WHERE `BadgeTypeID` = $id;";
    DB::run($sql);

}


function save_badge($badge)
{
    $id = $badge->Id;
    $name = MyPDO::quote($badge->Name);
    $event = $badge->Event;
    $cost = $badge->Cost;
    if ($badge->From == '') {
        $from = '0000-00-00';
    } else {
        $from = $badge->From;
    }
    if ($badge->To == '') {
        $to = '0000-00-00';
    } else {
        $to = $badge->To;
    }
    if ($id == -1) {
        $sql = <<<SQL
    INSERT INTO `BadgeTypes` (
        `BadgeTypeID`, `AvailableFrom`, `AvailableTo`, `Cost`, `EventID`,
        `Name`
    )
    VALUES
        (
            NULL, '$from', '$to', $cost, $event, $name
        )
SQL;
    } else {
        $sql = <<<SQL
    UPDATE
        `BadgeTypes`
    SET
        `AvailableFrom` = '$from',
        `AvailableTo` = '$to',
        `Cost` = $cost,
        `EventID` = $event,
        `Name` = '$name'
    WHERE
        `BadgeTypeID` = $id
SQL;
    }
    DB::run($sql);

}


function save_event($event)
{
    $id = $event->Id;
    $name = MyPDO::quote($event->Name);
    $from = $event->From;
    $to = $event->To;

    $cycle_to = lookup_cycleID($to);
    $cycle_from = lookup_cycleID($from);
    if ($cycle_to != $cycle_from) {
        header("HTTP/1.0 404");
        return;
    }

    if ($id == -1) {
        $sql = <<<SQL
    INSERT INTO `Events` (
        `EventID`, `AnnualCycleID`, `DateFrom`, `DateTo`, `EventName`
    )
    VALUES
        (
            NULL, '$cycle_from', '$from', '$to', $name
        )
SQL;
    } else {
        $sql = <<<SQL
    UPDATE
        `Events`
    SET
        `DateFrom` = '$from',
        `DateTo` = '$to',
        `AnnualCycleID` = $cycle_from,
        `EventName` = $name
    WHERE
        `EventID` = $id
SQL;
    }
    DB::run($sql);

}


function delete_event($id)
{
    $sql = "DELETE FROM `Events` WHERE `EventID` = $id;";
    DB::run($sql);

}
