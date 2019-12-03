<?php

/*.
    require_module 'standard';
.*/

require_once(__DIR__."/../functions/functions.inc");
require_once(__DIR__."/../backends/asset.inc");


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


$cmd = "pgrep -f ".basename(__FILE__);
exec($cmd, $pids);
if (!empty($pids)) {
    if (count($pids) > 1 || (int)$pids[0] != getmypid()) {
        print "Sync already in progress";
        exit();
    }
}


do_Neon_import();
