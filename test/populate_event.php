<?php

require_once(__DIR__."/../functions/functions.inc");
require_once(__DIR__."/../functions/database.inc");
require_once(__DIR__."/../functions/users.inc");
require_once(__DIR__."/../functions/authentication.inc");
require_once(__DIR__."/../modules/event/functions/functions.inc");
require_once(__DIR__."/../modules/concom/functions/concom.inc");


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


function rand_word($len)
{
    $out = '';
    $c  = 'bcdfghjklmnprstvwz'; // consonants except hard to speak ones
    $v  = 'aeiou';              // vowels
    $a  = $c.$v;                // all

    for ($i = 0; $i < $len; $i++) {
        $sy = $c[rand(0, strlen($c) - 1)];
        $sy .= $v[rand(0, strlen($v) - 1)];
        $sy .= $a[rand(0, strlen($a) - 1)];
        if (rand(0, 1) == 0) {
            $sy = ucfirst($sy);
        }
        $out .= $sy;
    }
    return $out;

}


function rand_name()
{
    $fn = '';
    /*.string.*/ $mn = null;
    $ln = '';
    $l = 0;

    $s = rand(0, 1) + 1;
    $fn = rand_word($s);

    if (rand(0, 1) > 0 && $l < 3) {
        $s = rand(0, 1) + 1;
        $mn = rand_word($s);
        $l++;
    }

    $s = rand(0, 2) + 2;
    $ln  = rand_word($s);

    return [$fn, $mn, $ln];

}


function listStates()
{
    return array(
        ['code' => 'AL', 'name' => 'ALABAMA'],
        ['code' => 'AK', 'name' => 'ALASKA'],
        ['code' => 'AS', 'name' => 'AMERICAN SAMOA'],
        ['code' => 'AZ', 'name' => 'ARIZONA'],
        ['code' => 'AR', 'name' => 'ARKANSAS'],
        ['code' => 'CA', 'name' => 'CALIFORNIA'],
        ['code' => 'CO', 'name' => 'COLORADO'],
        ['code' => 'CT', 'name' => 'CONNECTICUT'],
        ['code' => 'DE', 'name' => 'DELAWARE'],
        ['code' => 'DC', 'name' => 'DISTRICT OF COLUMBIA'],
        ['code' => 'FM', 'name' => 'FEDERATED STATES OF MICRONESIA'],
        ['code' => 'FL', 'name' => 'FLORIDA'],
        ['code' => 'GA', 'name' => 'GEORGIA'],
        ['code' => 'GU', 'name' => 'GUAM GU'],
        ['code' => 'HI', 'name' => 'HAWAII'],
        ['code' => 'ID', 'name' => 'IDAHO'],
        ['code' => 'IL', 'name' => 'ILLINOIS'],
        ['code' => 'IN', 'name' => 'INDIANA'],
        ['code' => 'IA', 'name' => 'IOWA'],
        ['code' => 'KS', 'name' => 'KANSAS'],
        ['code' => 'KY', 'name' => 'KENTUCKY'],
        ['code' => 'LA', 'name' => 'LOUISIANA'],
        ['code' => 'ME', 'name' => 'MAINE'],
        ['code' => 'MH', 'name' => 'MARSHALL ISLANDS'],
        ['code' => 'MD', 'name' => 'MARYLAND'],
        ['code' => 'MA', 'name' => 'MASSACHUSETTS'],
        ['code' => 'MI', 'name' => 'MICHIGAN'],
        ['code' => 'MN', 'name' => 'MINNESOTA'],
        ['code' => 'MS', 'name' => 'MISSISSIPPI'],
        ['code' => 'MO', 'name' => 'MISSOURI'],
        ['code' => 'MT', 'name' => 'MONTANA'],
        ['code' => 'NE', 'name' => 'NEBRASKA'],
        ['code' => 'NV', 'name' => 'NEVADA'],
        ['code' => 'NH', 'name' => 'NEW HAMPSHIRE'],
        ['code' => 'NJ', 'name' => 'NEW JERSEY'],
        ['code' => 'NM', 'name' => 'NEW MEXICO'],
        ['code' => 'NY', 'name' => 'NEW YORK'],
        ['code' => 'NC', 'name' => 'NORTH CAROLINA'],
        ['code' => 'ND', 'name' => 'NORTH DAKOTA'],
        ['code' => 'MP', 'name' => 'NORTHERN MARIANA ISLANDS'],
        ['code' => 'OH', 'name' => 'OHIO'],
        ['code' => 'OK', 'name' => 'OKLAHOMA'],
        ['code' => 'OR', 'name' => 'OREGON'],
        ['code' => 'PW', 'name' => 'PALAU'],
        ['code' => 'PA', 'name' => 'PENNSYLVANIA'],
        ['code' => 'PR', 'name' => 'PUERTO RICO'],
        ['code' => 'RI', 'name' => 'RHODE ISLAND'],
        ['code' => 'SC', 'name' => 'SOUTH CAROLINA'],
        ['code' => 'SD', 'name' => 'SOUTH DAKOTA'],
        ['code' => 'TN', 'name' => 'TENNESSEE'],
        ['code' => 'TX', 'name' => 'TEXAS'],
        ['code' => 'UT', 'name' => 'UTAH'],
        ['code' => 'VT', 'name' => 'VERMONT'],
        ['code' => 'VI', 'name' => 'VIRGIN ISLANDS'],
        ['code' => 'VA', 'name' => 'VIRGINIA'],
        ['code' => 'WA', 'name' => 'WASHINGTON'],
        ['code' => 'WV', 'name' => 'WEST VIRGINIA'],
        ['code' => 'WI', 'name' => 'WISCONSIN'],
        ['code' => 'WY', 'name' => 'WYOMING'],
        ['code' => 'AE', 'name' => 'ARMED FORCES AFRICA \ CANADA \ EUROPE \ MIDDLE EAST'],
        ['code' => 'AA', 'name' => 'ARMED FORCES AMERICA (EXCEPT CANADA)'],
        ['code' => 'AP', 'name' => 'ARMED FORCES PACIFIC']
    );

}


function rand_address()
{
    $data = array();

    $num = rand(100, 9999);
    $s = rand(0, 3) + 2;
    $data['addressLine1'] = $num.' '.rand_word($s);
    $data['city'] = 'Minneapolis';
    $states = listStates();
    $randIndex = array_rand($states);
    $data['state'] = $states[$randIndex]['code'];
    $data['zipCode'] = strval(rand(10000, 99999));
    $counties = listCountries();
    $randIndex = array_rand($counties);
    $data['country'] = $counties[$randIndex]['id'];
    $data['phone1'] = strval(rand(1111111111, 9999999999));

    return $data;

}


function advance_cycles()
{
    $sql = "SELECT * FROM `AnnualCycles` WHERE DateTo = (SELECT MAX(DateTo) FROM `AnnualCycles`);";
    $result = \DB::run($sql);
    $value = $result->fetch();
    $last_end = $value['DateTo'];
    $last_start = $value['DateFrom'];
    $target = strtotime(' +183 day');
    while (strtotime($last_end) < $target) {
        print "Add new cycle\n";
        $last_start = date('Y-m-d', strtotime($last_start." + 365 day"));
        $last_end = date('Y-m-d', strtotime($last_end." + 365 day"));
        new_cycle($last_start, $last_end);
    }

}


function new_cycle($start, $end)
{
    print "$start - $end\n";
    $sql = <<<SQL
            INSERT INTO `AnnualCycles`
                        (`AnnualCycleID`, `DateFrom`, `DateTo`)
                        VALUES (NULL, "$start", "$end")
SQL;
    \DB::run($sql);

}


function add_event()
{
    $event = \current_eventID();
    if ($event === null) {
        advance_cycles();
        print "Add new event\n";

        $event = (object)[
        "Name" => "WOW a test Event-Con",
        "Id" => -1,
        "From" => date('Y-m-d', strtotime(" + 180 day")),
        "To" => date('Y-m-d', strtotime(" + 183 day"))
        ];

        save_event($event);
    }

}


function populate_badges($event)
{
    if ($event === null) {
        print "Event not yet defined\n";
        return;
    }

    $sql = "SELECT * FROM `BadgeTypes`";
    $result = \DB::run($sql);
    $value = $result->fetch();
    if ($value === false) {
        print "Add event badges\n";
        for ($i = 0; $i < 5; $i++) {
            $badge = (object)[
            "Id" => -1,
            "Event" => $event,
            "Name" => 'Badge Type '.$i,
            "Cost" => ($i + 1) * 10,
            "Image" => "",
            "From" => date('Y-m-d', strtotime("now")),
            "To" => date('Y-m-d', strtotime(" + 180 day"))
            ];
            save_badge($badge);
        }
    }

}


function random_member()
{
    $name = rand_name();
    $email = $name[0].$name[2].rand(1, 9999).'@email.net';
    $updateData = rand_address();
    $updateData['firstName'] = $name[0];
    $updateData['middleName'] = $name[1];
    $updateData['lastName'] = $name[2];

    $account = createUser($email);
    if ($account) {
        $updateData['accountId'] = $account;
        updateAccount($updateData, $account);
    } else {
        random_member();
    }

}


function populate_members()
{
    $sql = "SELECT COUNT(`AccountID`) AS count FROM  `Members`;";
    $result = \DB::run($sql);
    $value = $result->fetch();
    $t = intval($value['count']);
    if ($t < 5001) {
        print "populate ".(strval(5001 - $t))." members\n";
        for ($i = 0; $i < 5001 - $t; $i++) {
            random_member();
        }
    }

}


function rand_member($count = 1)
{
    $sql = <<<SQL
        SELECT AccountID FROM `Members`
        ORDER BY RAND()
        LIMIT $count
SQL;
    $result = DB::run($sql);
    if ($count == 1) {
        $value = $result->fetch();
        return (int)($value['AccountID']);
    }
    $values = $result->fetchAll();
    $result = [];
    foreach ($values as $v) {
        $result[] = $v['AccountID'];
    }
    return $result;

}


function populate_concom()
{
    $sql = "SELECT COUNT(`AccountID`) AS count FROM  `ConComList`;";
    $result = \DB::run($sql);
    $value = $result->fetch();
    if (intval($value['count']) > 999) {
        return;
    }

    print "Populate concom\n";

    $sql = "SELECT * FROM `Departments`;";
    $result = \DB::run($sql);
    $value = $result->fetch();
    while ($value !== false) {
        if ($value['Name'] == "Historical Placeholder") {
            $value = $result->fetch();
            continue;
        }
        if ($value['DepartmentID'] == $value['ParentDepartmentID']) {
            AddConComPosition(1000, $value['Name'], 'Head', '');
        } else {
            $aid = rand_member();
            AddConComPosition($aid, $value['Name'], 'Head', '');
        }
        $aid = rand_member();
        AddConComPosition($aid, $value['Name'], 'Sub-Head', '');
        $aid = rand_member();
        AddConComPosition($aid, $value['Name'], 'Specialist', '');
        $aid = rand_member();
        AddConComPosition($aid, $value['Name'], 'Specialist', '');
        $aid = rand_member();
        AddConComPosition($aid, $value['Name'], 'Specialist', '');
        $value = $result->fetch();
    }

}


function random_badge($event)
{
    $sql = <<<SQL
        SELECT BadgeTypeID FROM `BadgeTypes`
        WHERE `EventID` = $event
        ORDER BY RAND()
        LIMIT 1
SQL;
    $result = DB::run($sql);
    $value = $result->fetch();
    return (int)($value['BadgeTypeID']);

}


function add_registration($aid, $bid, $event)
{
    $accountID  = $aid;
    $type = random_badge($event);
    $badge = MyPDO::quote(rand_word(3));

    $sql = <<<SQL
        INSERT INTO `Registrations` (
            RegistrationID, AccountID, EventID,
            RegisteredByID, RegistrationDate,
            BadgesPickedUp, BadgeName, BadgeTypeID,
            EmergencyContact
        )
        VALUES
            (
                NULL, $accountID, $event, $bid,
                NOW(), 0, $badge, $type, NULL
            );
SQL;
    DB::run($sql);

}


function populate_registrations($event)
{
    $sql = "SELECT COUNT(`RegistrationID`) AS count FROM  `Registrations`;";
    $result = \DB::run($sql);
    $value = $result->fetch();
    if (intval($value['count']) > 0) {
        return;
    }

    print "Populate Registrations\n";

    $sql = "SELECT COUNT( DISTINCT `AccountID`) AS count FROM  `ConComList`;";
    $result = \DB::run($sql);
    $value = $result->fetch();
    $count = intval($value['count']) * 2;
    $members = rand_member($count);
    $i = 0;

    $sql = "SELECT DISTINCT `AccountID` FROM  `ConComList`;";
    $result = \DB::run($sql);
    $value = $result->fetch();
    while ($value !== false) {
        add_registration($members[$i], $value['AccountID'], $event);
        $i++;
        add_registration($members[$i], $value['AccountID'], $event);
        $i++;
        $value = $result->fetch();
    }

}


_config_from_Database();
print "<pre>";
if (array_key_exists('NEONID', $GLOBALS) &&
    array_key_exists('NEONKEY', $GLOBALS) &&
    !empty($GLOBALS['NEONID']) &&
    !empty($GLOBALS['NEONKEY'])) {
    die("We will not run this test script if you have Neon connected.");
}

add_event();
$event = \current_eventID();
populate_badges($event);
populate_members();
populate_concom();
populate_registrations($event);
include(__DIR__.'/populate_volunteer.php');
print "Done";
print "</pre>";
