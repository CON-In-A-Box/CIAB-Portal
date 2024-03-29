<?php

/*.
    require_module 'standard';
    require_module 'json';
.*/

require_once($FUNCTIONDIR."/database.inc");
require_once($BACKEND."/CRM.inc");


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
            AvailableTo,
            BackgroundImage
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
    'BackgroundImage' => 'BackgroundImage',
    ];
    $badge_data = load_table($sql, $values);
    $badge_keys = array_keys($values);

    print "<table class='UI-stripedtable UI-padding'>\n";
    print "<tr><th>".implode('</th><th>', $event_keys)."</th></tr>\n";
    foreach ($event_data as $entry) {
        $plain_name = htmlspecialchars($entry['Name'], ENT_QUOTES);
        $entry['Name'] .= " <i id='badges_".$entry['Id']."_arrow' class='fas fa-caret-down'>";
        print "<tr class='event-hover-primary' ";
        print "onclick='expandEvent(\"badges_".$entry['Id']."\")'>";
        print "<td>".implode('</td><td>', $entry)."</td>";
        print "</tr>\n";

        print "<tr><td colspan=5><div name='badges_".$entry['Id']."' class=' UI-hide'><table class='UI-stripedtable UI-center'>";

        print "<tr>";
        unset($badge_keys[0]);
        unset($badge_keys[3]);
        unset($badge_keys[5]);
        unset($badge_keys[6]);
        unset($badge_keys[7]);
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
                unset($badge['BackgroundImage']);
                print "<tr> ";
                print "<td>".implode('</td><td>', $badge)."</td>";
                print "<td class='UI-center'>";
                print "<button class='UI-small UI-eventbutton'  onclick='editBadge(\"$json\");'>Edit</button>  ";
                if (intval($badge['Sold']) == 0) {
                    $name = $badge['Badge Name'];
                    print "<button class='UI-small UI-redbutton' onclick='deleteBadge($id,\"$name\");'>Delete</button>";
                } else {
                    print "<button class='UI-small UI-redbutton' disabled>Delete</button>";
                }
                print "</td>";
                print "</tr>\n";
            }
        }
        print "<tr> <td colspan=4 class='UI-center'>";
        $event = $entry['Id'];
        print "<button id='new' class='UI-eventbutton' onclick='newBadge($event, \"$plain_name\")'>Add New Badge</button>";
        print "</td></tr>";
        print "</table>";
        print "</div></td>";
        print "<td> <div name='badges_".$entry['Id']."' class=' UI-hide'>";

        $details = _event_details($entry['Id']);
        $can_delete = true;
        print "<table class='UI-stripedtable'>";
        foreach ($details as $key => $detail) {
            print "<tr><td>".$key."</td><td>";
            print $detail;
            if ($can_delete && intval($detail) > 0) {
                $can_delete = false;
            }
            if (intval($detail) === 0 && $key === 'concom' && count($event_data) > 1) {
                print "&nbsp;&nbsp;<button id='import' class='UI-eventbutton' onclick='importConcom(".$entry['Id'].")'>Import</button>";
            }
            print "</td></tr>";
        }
        print "</table>";
        print "<div class='UI-center'>";
        $json = json_encode($entry);
        $json = base64_encode($json);
        print "<button class='UI-eventbutton UI-margin' onclick='editEvent(\"$plain_name\",\"$json\")'>Modify Event</button>";
        print "</div>";
        print "<div class='UI-center'>";
        if ($can_delete) {
            print "<button class='UI-redbutton' onclick='deleteEvent(".$entry['Id'].",\"$plain_name\")'>Delete Event</button>";
        } else {
            print "<button class='UI-redbutton UI-disabled' >Delete Event</button>";
        }
        print "</div>";
        print "</div></td>";
        print "</tr>";
    }
    print "</table>";

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
    print "<table class='UI-stripedtable'>\n";
    print "<tr><th>".implode('</th><th>', $keys)."</th><th></th><th></th></tr>\n";
    foreach ($data as $entry) {
        $json = json_encode($entry);
        $json = base64_encode($json);
        print "<tr><td>".implode('</td><td>', $entry);
        print "<td> <button class='UI-eventbutton' onclick='editMeeting(\"$json\")'>Modify Meeting</button> </td>";
        $attendance = _check_attendance($entry['Id']);
        if ($attendance > 0) {
            print "<td class='UI-center'> <span class='UI-redbutton UI-disabled'>".$attendance." Attended </span> </td>";
        } else {
            print "<td class='UI-center'> <button class='UI-redbutton' onclick='deleteMeeting(\"".$entry['Name'].'",'.$entry['Id'].")'>Delete Meeting</button> </td>";
        }
        print "</tr>\n";
    }
    print "</table>\n";

}


function import_meetings()
{
    if (\ciab\CRM::hasEvents()) {
        \ciab\CRM::importMeetings();
    }

}


function import_events()
{
    if (\ciab\CRM::hasEvents()) {
        \ciab\CRM::importEvents();
    }

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
    $image = MyPDO::quote($badge->Image);
    if ($badge->From == '') {
        $from = MyPDO::quote('0000-00-00');
    } else {
        $from = MyPDO::quote($badge->From);
    }
    if ($badge->To == '') {
        $to = MyPDO::quote('0000-00-00');
    } else {
        $to = MyPDO::quote($badge->To);
    }
    if ($id == -1) {
        $sql = <<<SQL
    INSERT INTO `BadgeTypes` (
        `BadgeTypeID`, `AvailableFrom`, `AvailableTo`, `Cost`, `EventID`,
        `Name`, `BackgroundImage`
    )
    VALUES
        (
            NULL, $from, $to, $cost, $event, $name, $image
        )
SQL;
    } else {
        $sql = <<<SQL
    UPDATE
        `BadgeTypes`
    SET
        `AvailableFrom` = $from,
        `AvailableTo` = $to,
        `Cost` = $cost,
        `EventID` = $event,
        `Name` = $name,
        `BackgroundImage` = $image
    WHERE
        `BadgeTypeID` = $id
SQL;
    }
    DB::run($sql);

}


function duplicate_concom($to, $from)
{
    $sql = <<<SQL
    INSERT INTO `ConComList` (
        EventID, AccountID, DepartmentID,
        Note, PositionID
    )
    SELECT
        "$to",
        AccountID,
        DepartmentID,
        Note,
        PositionID
    FROM
        `ConComList`
    WHERE
        EventID = "$from"
        AND DepartmentID NOT IN (
            SELECT
                `DepartmentID`
            FROM
                `Departments`
            WHERE
                Name = 'Historical Placeholder'
        )
SQL;
    DB::run($sql);

}


function attendance_report($event = null)
{
    if ($event === null) {
        $event = \current_eventID();
    }

    return <<<SQL
        SELECT
            AccountID,
            Email,
            COUNT(Name) as Count,
            GROUP_CONCAT(Name SEPARATOR ', ') as Meetings
        FROM (
            SELECT ma.AccountID as AccountID,
                   m.Email as Email,
                   om.Name as Name,
                   om.Date as Date
            FROM MeetingAttendance AS ma
            INNER JOIN OfficialMeetings AS om ON ma.MeetingID = om.MeetingID
            INNER JOIN Members AS m ON ma.AccountID = m.AccountID
            WHERE `EventID` = '$event'
            ORDER BY Date ASC
        ) AS attendees
        GROUP BY AccountID
        ORDER BY Count DESC
SQL;

}


function cycle_attendance_report($cycle = null)
{
    $dateFrom = $cycle['DateFrom'];
    $dateTo = $cycle['DateTo'];

    return <<<SQL
        SELECT
            AccountID,
            Email,
            COUNT(Name) as Count,
            GROUP_CONCAT(Name SEPARATOR ', ') as Meetings
        FROM (
            SELECT ma.AccountID as AccountID,
                   m.Email as Email,
                   om.Name as Name,
                   om.Date as Date
            FROM MeetingAttendance AS ma
            INNER JOIN OfficialMeetings AS om ON ma.MeetingID = om.MeetingID
            INNER JOIN Members AS m ON ma.AccountID = m.AccountID
            WHERE Date >= '$dateFrom' AND Date <= '$dateTo'
            ORDER BY Date ASC
        ) AS attendees
        GROUP BY AccountID
        ORDER BY Count DESC
SQL;

}
