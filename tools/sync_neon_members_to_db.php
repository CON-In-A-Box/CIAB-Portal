<?php

/*.
    require_module 'standard';
.*/
require __DIR__."/../vendor/autoload.php";

if (is_file(__DIR__.'/../.env')) {
    $dotenv = Dotenv\Dotenv::create(__DIR__.'/../');
    $dotenv->load();
}

require_once(__DIR__."/../functions/functions.inc");
require_once(__DIR__."/neon_members_lib.inc");
require_once(__DIR__."/neon_sync_tools.inc");

$SILENCE_LOG = true;


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


verify_single_process(basename(__FILE__));

initializeApplication();
do_Neon_import();
