<?php

/*.
    require_module 'standard';
.*/

require_once(__DIR__.'/functions.inc');
require_once(__DIR__.'/session.inc');
if (array_key_exists('Neon', $GLOBALS)) {
    require_once(__DIR__.'/neon.inc');
}


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
    if (array_key_exists('Neon', $GLOBALS)) {
        loadDefinedFields();
        if ($from < 2018011700) {
            require_once(__DIR__."/update/from_neon_to_152.inc");
            from_neon_to_152();
            _update_DB_Version(2018011700);
        }
        if ($from < 2018091700) {
            require_once(__DIR__."/update/to_180501.inc");
            to_180501();
            _update_DB_Version(2018091700);
        }
        if ($from < 2018092400) {
            require_once(__DIR__."/update/to_180924.inc");
            to_180924();
            _update_DB_Version(2018092400);
        }
    }
    _update_DB_Version($to);

}
