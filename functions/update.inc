<?php

/*.
    require_module 'standard';
.*/

require_once(__DIR__.'/functions.inc');
require_once(__DIR__.'/session.inc');
require_once($BACKEND.'/CRM.inc');


function _update_DB_Version($target)
{
    $sql = <<<SQL
        UPDATE Configuration
        SET Value = '$target'
        WHERE Field = 'DBSchemaVersion';
SQL;
    DB::run($sql);
    echo "<br><hr>";
    echo "<h3>Database Schema updated to: $target</h3>";
    echo "<h3>Done!</h3>";

}


function db_do_update($from, $to)
{
    if (\ciab\CRM::hasUpdates()) {
        \ciab\CRM::update($from, $to);
    }
    if ($from < 2019121600) {
        require_once(__DIR__."/update/to_191216.inc");
        to_191216();
        _update_DB_Version(2019121600);
    }
    _update_DB_Version($to);

}
