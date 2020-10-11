<?php

require_once(__DIR__."/../functions/functions.inc");
require_once(__DIR__."/../functions/database.inc");
require_once(__DIR__."/../functions/users.inc");
require_once(__DIR__."/../functions/authentication.inc");
require_once(__DIR__."/../modules/event/functions/functions.inc");
require_once(__DIR__."/../modules/concom/functions/concom.inc");


function rand_word($len)
{
    $out = '';
    $c  = 'bcdfghjklmnprstvwz'; // consonants except hard to speak ones
    $v  = 'aeiou';              // vowels
    $a  = $c.$v;                // all

    for ($i = 0; $i <= $len; $i++) {
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

    $s = rand(0, 1) + 1;
    $fn = rand_word($s);

    if (rand(0, 1) > 0) {
        $s = rand(0, 1) + 1;
        $mn = rand_word($s);
    }

    $s = rand(0, 2) + 2;
    $ln  = rand_word($s);

    return [$fn, $mn, $ln];

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
    $email = $name[0].'@email.net';
    $updateData = rand_address();
    $updateData['firstName'] = $name[0];
    $updateData['middleName'] = $name[1];
    $updateData['lastName'] = $name[2];

    $account = createUser($email);
    $updateData['accountId'] = $account;
    updateAccount($updateData);

}


function populate_members()
{
    $sql = "SELECT COUNT(`AccountID`) AS count FROM  `Members`;";
    $result = \DB::run($sql);
    $value = $result->fetch();
    $t = intval($value['count']);
    if ($t < 501) {
        print "populate ".(strval(501 - $t))." members\n";
        for ($i = 0; $i < 501 - $t; $i++) {
            random_member();
        }
    }

}


function rand_member()
{
    $sql = <<<SQL
        SELECT AccountID FROM `Members`
        ORDER BY RAND()
        LIMIT 1
SQL;
    $result = DB::run($sql);
    $value = $result->fetch();
    return (int)($value['AccountID']);

}


function populate_concom()
{
    $sql = "SELECT COUNT(`AccountID`) AS count FROM  `ConComList`;";
    $result = \DB::run($sql);
    $value = $result->fetch();
    if (intval($value['count']) > 0) {
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


function add_registration($aid, $event)
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
                NULL, $accountID, $event, $accountID,
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

    $sql = "SELECT `AccountID` FROM  `ConComList`;";
    $result = \DB::run($sql);
    $value = $result->fetch();
    while ($value !== false) {
        add_registration($value['AccountID'], $event);
        $value = $result->fetch();
    }

    for ($i = 0; $i < 100; $i++) {
        $accountID  = rand_member();
        add_registration($accountID, $event);
    }

}


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
