<?php

/*.
    require_module 'standard';
.*/

require_once(__DIR__.'/functions.inc');

function _update_DB_Version($target)
{
    global $db;
    $sql = <<<SQL
        UPDATE Configuration
        SET Value = '$target'
        WHERE Field = 'DBSchemaVersion';
SQL;
    $db->run($sql);
    echo "<br><hr>";
    echo "<h3>Database Schema updated to: $target</h3>";
    echo "<h3>Done!</h3>";

}


function db_do_update($from)
{
    if ($from < 2018011700) {
        require_once(__DIR__."/update/from_neon_to_152.inc");
        from_neon_to_152();
        _update_DB_Version(2018011700);
    } 
    if ($from < 2018030200) {
        require_once(__DIR__."/update/from_152_to_153.inc");
        from_152_to_153();
        _update_DB_Version(2018030200);
    }

}
