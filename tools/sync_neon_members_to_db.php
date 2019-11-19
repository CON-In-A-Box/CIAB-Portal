<?php

/*.
    require_module 'standard';
.*/

require_once(__DIR__."/../functions/functions.inc");
require_once(__DIR__."/neon_members_lib.inc");


function _Neon_import_members($fields)
{
    /* When really ready switch to true */
    $page = 1;
    $total = 0;
    $round = 0;
    do {
        $people = _lookup_members($fields, $page, null, false);
        $count = count($people['members']);
        if ($count) {
            $round = _import_page_of_people($people, $page);
            $total += $round;
        } else {
            break;
        }
        print $page.": ".count($people['members'])." ".$total."\n";
        if ($round != $count) {
            print "Person count missmatch. Neon has ".$count." people and we ";
            print "imported ".$total." people.";
        }
        $page++;
    } while (true);
    print "done\n";

}


function do_Neon_import()
{
    $fields = _loadEventCustomFields();
    _Neon_import_members($fields);

}


$cmd = "pgrep -f ".basename(__FILE__);
exec($cmd, $pids);
if (!empty($pids)) {
    if (count($pids) > 1 || (int)$pids[0] != getmypid()) {
        print "Sync already in progress";
        exit();
    }
}


do_Neon_import();
