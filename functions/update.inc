<?php

/*.
    require_module 'standard';
.*/

require_once(__DIR__.'/functions.inc');
require_once(__DIR__."/update/from_neon_to_152.inc");


function _update_DB_Version()
{
    global $db;
    $target = DB::$REQUIED_DB_SCHEMA;
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
        from_neon_to_152();
    }
    _update_DB_Version();

}
