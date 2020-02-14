<?php

/*.
    require_module 'standard';
.*/


function verify_single_process(string $file)
{
    $cmd = "pgrep -f ".$file;
    exec($cmd, $pids);
    if (!empty($pids)) {
        $mypid = getmypid();
        foreach ($pids as $p) {
            if (intval($p) == $mypid) {
                continue;
            }
            $cmd = exec("ps -p $p -o comm=");
            if (strstr($cmd, 'php') !== false) {
                print "$mypid: Sync already in progress ($p)\n";
                exit();
            }
        }
    }

}
