<?php

require_once(__DIR__."/../functions/functions.inc");
require_once(__DIR__."/../functions/database.inc");
require_once(__DIR__."/../modules/volunteers/functions/volunteer.inc");


function random_concom_id()
{
    $sql = <<<SQL
        SELECT AccountID FROM ConComList
        ORDER BY RAND()
        LIMIT 1
SQL;
    $result = DB::run($sql);
    $value = $result->fetch();
    return (int)($value['AccountID']);

}


function random_department()
{
    $sql = <<<SQL
        SELECT Name FROM Departments
        ORDER BY RAND()
        LIMIT 1
SQL;
    $result = DB::run($sql);
    $value = $result->fetch();
    return $value['Name'];

}


function clean()
{
    $sql = "DELETE FROM `VolunteerHours` WHERE 1";
    DB::run($sql);
    $sql = "DELETE FROM `HourRedemptions` WHERE 1";
    DB::run($sql);
    $sql = "DELETE FROM `VolunteerRewards` WHERE 1";
    DB::run($sql);
    $sql = "DELETE FROM `RewardGroup` WHERE 1";
    DB::run($sql);

}


function populate_vol()
{
    print "Populate Hours\n";

    $id = 0;
    $enterer = 0;
    $authorized = 0;

    for ($i = 0; $i < 5000; $i++) {
        $id = random_concom_id();
        $enterer = random_concom_id();
        $authorized = random_concom_id();
        $department = random_department();
        $modifier = rand(1, 4) * 0.5;
        $hours = rand(1, 5);
        $end = date("Y-m-d H:i:s");
        record_volunteer_hours($id, $hours, $end, $modifier, $department, $enterer, $authorized);
    }

}


function populate_prizes()
{
    print "Populate Gifts\n";

    /* Add 5 groups */
    for ($i = 0; $i < 5; $i++) {
        $limit = rand(1, 4);
        $group = add_volunteer_prize_group();
        update_volunteer_prize_group($group, $limit);
    }

    $sql = "SELECT RewardGroupID FROM RewardGroup LIMIT 1;";
    $result = DB::run($sql);
    $value = $result->fetch();
    $bottom = (int)($value['RewardGroupID']);

    $adj = ['red', 'cool', 'fantasy', 'large', 'mystic', 'silly', 'filly',
    'green', 'yellow', 'black', 'old', 'new', 'plastic', 'sf',
    'fantasy', 'small', 'white', 'branded', 'plain'];
    $noun = ['ribbon', 'pencil', 'fan', 'bag', 'shirt', 'bag', 'lanyard',
    'poster', 'book', 'hat'];


    /* Add grouped promo item */
    $n1 = $noun[array_rand($noun)];
    $value = (float)rand(0, 5);
    $value += rand(1, 9) / 10;
    $promo = 1;
    for ($i = 0; $i < 5; $i++) {
        $inventory = rand(10, 500);
        $name = $adj[array_rand($adj)].' '.$n1;
        $group = $bottom;
        new_volunteer_prize($name, $value, $promo, $group, $inventory);
    }

    /* Add a bunch of items */

    $promo_count = 0;
    $group_count = 1;

    $total_items = 50;

    for ($i = 0; $i < 50; $i++) {
        $value = (float)rand(0, 5);
        $value += rand(1, 9) / 10;
        $inventory = rand(10, 500);
        $name = $adj[array_rand($adj)].'-'.$adj[array_rand($adj)].' '.$noun[array_rand($noun)];
        $group = null;
        if ($promo_count < $total_items / 10) {
            $promo = rand(0, 1);
            $promo_count++;
        } else {
            $promo = 0;
            if ($group_count < $total_items / 4 && rand(0, 1)) {
                $group = $bottom + rand(1, 4);
                $group_count++;
            }
        }
        new_volunteer_prize($name, $value, $promo, $group, $inventory);
    }

}


function populate_redeem()
{
    print "Populate Redeem\n";

    $sql = <<<SQL
        SELECT AccountID FROM ConComList;
SQL;
    $result = DB::run($sql);
    $value = $result->fetch();
    while ($value != false) {
        $id = (int)($value['AccountID']);

        $sql = <<<SQL
            SELECT * FROM VolunteerRewards
            ORDER BY RAND()
            LIMIT 10
SQL;
        $result2 = DB::run($sql);
        $value2 = $result2->fetch();
        $prizes = [];
        while ($value2 != false) {
            $prizes[] = $value2['PrizeID'];
            $value2 = $result2->fetch();
        }
        award_prizes($id, $prizes);
        $value = $result->fetch();
    }

}


clean();
populate_vol();
populate_prizes();
populate_redeem();
print "Complete\n";
