<?php

/*.
    require_module 'standard';
.*/

// Currently, this database class is set for the MySQL PDO filter/backend. Future version will expand it to be agnostic

require_once($BACKEND.'/'.$DB_BACKEND); // MyPDO Definition used

class DB
{
    private static function _getCurrentDBVersion()
    {
        /* check if we have a database at all */
        $found = false;
        $result = MyPDO::instance()->query("SHOW TABLES LIKE 'Configuration';");
        foreach ($result as $value)
            $found = true;
        if (!$found)
            return false;

        $sql = <<<SQL
            SELECT Value AS DBSchemaVersion
            FROM Configuration
            WHERE Field = 'DBSchemaVersion';
SQL;
        $result = self::run($sql);
        $value = $result->fetch();
        if ($value === false) {
            return false;
        } else {
            return $value['DBSchemaVersion'];
        }
    }

    private static function _buildMissingTables()
    {
        $DATADIR = $GLOBALS['BASEDIR'].'/data';

        $result = MyPDO::instance()->query("SHOW TABLES");
        $arr = [];
        foreach ($result as $value) {
            foreach ($value as $row) {
                $arr[] = $row;
            }
        }

        foreach (SCHEMA::$DB_tables as $table => $fields) {
            if (!in_array($table, $arr)) {
                // Missing a table, build it!
                $build = 'CREATE TABLE '.$table.' (';
                foreach ($fields as $column => $setting) {
                    $build .= $column.' '.$setting.', ';
                }
                $build = rtrim($build, ', ').");";

                MyPDO::instance()->query($build);
                // If there is seed available for this table, seed it!
                if (file_exists($DATADIR.'/DBSeed/'.$table.'.sql')) {
                    $sql_data = file_get_contents($DATADIR.'/DBSeed/'.$table.'.sql');
                    MyPDO::instance()->query($sql_data);
                }
            }
        }
        
        $build = "SELECT TABLE_NAME, COLUMN_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = '".$GLOBALS['DBNAME']."' AND CONSTRAINT_NAME <> 'PRIMARY';";
        $result = MyPDO::instance()->query($build);
        $arr = [];
        foreach ($result as $value) {
           $arr[] = $value['TABLE_NAME'].":".$value['COLUMN_NAME'];
        }
        foreach (SCHEMA::$DB_foreignKeys as $table => $fields) {
            foreach ($fields as $column => $referto) {
                $lookfor = $table.":".$column;
                if (!in_array($lookfor, $arr)) {
                    $build = "ALTER TABLE ".$table;
                    $build .= " ADD FOREIGN KEY (".$column.") REFERENCES ".$referto.";";
                    MyPDO::instance()->query($build);
                }
            }
        }
    }

    public function __construct()
    {
        require_once($GLOBALS['BASEDIR'].'/data/database-schema.php');
        DB::_buildMissingTables();
        if (!empty($_SESSION['username']) && !empty($_REQUEST['Function']) && $_REQUEST['Function'] != 'update') {
            $upgrade = DB::_getCurrentDBVersion();
            if ($upgrade != SCHEMA::$REQUIED_DB_SCHEMA) {
	        // If we have to do an update, do it
                header("Location: http://".$_SERVER['SERVER_NAME']."/index.php?Function=update&from=".$upgrade."&to=".SCHEMA::$REQUIED_DB_SCHEMA);
            }
        }

    }


    public static function run($sql, $args = [])
    {
        if (!$args) {
            return MyPDO::instance()->query($sql);
        }
        $stmt = MyPDO::instance()->prepare($sql);
        $stmt->execute($args);
        return $stmt;

    }


}


$_currentYear = null;


function current_yearID()
{
    global $_currentYear, $db, $_SESSION;

    if ($_currentYear == null) {
        /* Place 1 - Look for it in the session */

        if (isset($_SESSION['CurrentYear'])) {
            $sql = "SELECT YearID FROM ConventionYear WHERE Name = '".$_SESSION['CurrentYear']."';";
            $result = $db->run($sql);
            $value = $result->fetch();
            if ($value) {
                $_currentYear = $value['YearID'];
                return $_currentYear;
            }
        }

        /* Place 2 - try to get it from the database */

        $sql = "SELECT Value FROM Configuration WHERE Field='CurrentYearID';";
        $result = $db->run($sql);
        $value = $result->fetch();
        if ($value) {
            $_currentYear = $value['CurrentYearID'];
            return $_currentYear;
        }

        /* Place 3 - current calander year */

        $year = date("Y");
        $sql = "SELECT YearID FROM ConventionYear WHERE Name ='$year';";
        $result = $db->run($sql);
        $value = $result->fetch();
        if ($value) {
            $_currentYear = $value['YearID'];
            return $_currentYear;
        }

        /* Fallback - Last year in database*/

        $sql = "SELECT TOP 1 YearID FROM ConventionYear ORDER BY YearID DESC;";
        $result = $db->run($sql);
        $value = $result->fetch();
        if ($value) {
            $_currentYear = $value['YearID'];
            return $_currentYear;
        }

        die("Could not get Current Convention Year");
    }
    return $_currentYear;

}


function dump_table_to_csv($table, $condition = null)
{
    global $db;

    $data = [];
    $sql = "DESCRIBE `$table`;";
    $result = $db->run($sql);
    $value = $result->fetch();
    $data[] = array();
    while ($value !== false) {
        $data[0][] = $value['Field'];
        $value = $result->fetch();
    }
    $sql = "SELECT * FROM `$table` $condition;";
    $result = $db->run($sql);
    $value = $result->fetch();
    while ($value !== false) {
        $data[] = array_values($value);
        $value = $result->fetch();
    }

    foreach($data as $line) {
        $text .= implode(",",  $line)."\n";
    }

    return $text;

}
