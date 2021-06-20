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
require_once(__DIR__."/../backends/asset.inc");
require_once(__DIR__."/neon_sync_tools.inc");

$SILENCE_LOG = true;


function do_Neon_import()
{
    $sql = "SELECT AccountID FROM `Members` ORDER BY `AccountID` ASC;";
    $result = DB::run($sql);
    $value = $result->fetch();
    while ($value !== false) {
        try {
            $content = file_get_contents('https://www.z2systems.com/neon/resource/ce/images/account/'.$value['AccountID'].'/0_medium.jpg');
            if ($content === false) {
                $value = $result->fetch();
                continue;
            }
            \ciab\Asset::save('profile_'.$value['AccountID'], $content);
        } catch (Exception $e) {
        }
        $value = $result->fetch();
    }

}


verify_single_process(basename(__FILE__));

initializeApplication();
do_Neon_import();
