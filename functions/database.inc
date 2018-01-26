<?php

/*.
    require_module 'standard';
.*/

// Currently, this database class is set for the MySQL PDO filter/backend. Future version will expand it to be agnostic

require_once($BACKEND.'/'.$DB_BACKEND); // MyPDO Definition used

class DB
{


    public function __construct()
    {
        global $BASEDIR;
        $DATADIR = $BASEDIR.'/data';
        require_once($DATADIR.'/database-schema.php');

        $result = MyPDO::instance()->query("SHOW TABLES");
        $arr = [];
        foreach ($result as $value) {
            foreach ($value as $row) {
                $arr[] = $row;
            }
        }

        $upgrade = false;
        foreach ($DB_tables as $table => $fields) {
            if (!in_array($table, $arr)) {
                // Missing a table, build it!
                $upgrade = true;
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
        if ($upgrade) {
            header("Location: http://".$_SERVER['SERVER_NAME']."/index.php?Function=update&next=".$_REQUEST['Function']);
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
