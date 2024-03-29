<?php

/*.
    require_module 'standard';
    require_module 'json';
.*/

// Currently, this database class is set for the MySQL PDO filter/backend. Future version will expand it to be agnostic

require_once(__DIR__.'/locations.inc');
require_once($BACKEND.'/'.$_ENV['DB_BACKEND']); // MyPDO Definition used
require_once($BASEDIR.'/data/database-schema.php');

class DB
{


    private static function verifyDB()
    {
        /* check if we have a database at all */
        $result = MyPDO::instance()->query(<<<sql
            SELECT SCHEMA_NAME
              FROM INFORMATION_SCHEMA.SCHEMATA
             WHERE SCHEMA_NAME = '{$_ENV['DBNAME']}';
sql
        );
        if ($result->fetch() === false) {
            return false;
        }

        $result = MyPDO::instance()->query("SHOW TABLES LIKE 'Configuration';");
        if ($result->fetch() === false) {
            return false;
        }

        return true;

    }


    private static function getCurrentDBVersion()
    {
        if (!DB::verifyDB()) {
            return false;
        }

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


    public function __construct()
    {
        if (!empty($_REQUEST['Function']) && $_REQUEST['Function'] != 'update') {
            $upgrade = DB::getCurrentDBVersion();
            if ($upgrade != SCHEMA::$REQUIED_DB_SCHEMA) {
            // If we have to do an update, do it
                header("Location: ".$BASEURL."/index.php?Function=update&from=".$upgrade."&to=".SCHEMA::$REQUIED_DB_SCHEMA);
            }
        }

    }


    private static function dbLogEntry($sql)
    {
        if (substr(ltrim($sql), 0, 6) !== "SELECT") {
            // Prepare a log entry for this query
            $logMessage = MyPDO::instance()->prepare('INSERT INTO ActivityLog (AccountID, Function, Query) VALUES (:account, :function, :query);');

            if (empty($_SESSION['accountId'])) {
                $account = 0;
            } else {
                $account = $_SESSION['accountId'];
            }
            if (empty($_SESSION['Function'])) {
                $function = "public";
            } else {
                $function = $_SESSION['Function'];
            }

            // Use PDO to make the SQL Safe as it IS a SQL statement we are trying to capture
            $logMessage->bindParam(':account', $account, PDO::PARAM_INT);
            $logMessage->bindParam(':function', $function, PDO::PARAM_STR);
            $logMessage->bindParam(':query', $sql, PDO::PARAM_STR);

            // Write the log entry
            $logMessage->execute();
        }

    }


    public static function run($sql, $args = [])
    {
        if (!(array_key_exists('SILENCE_LOG', $GLOBALS) && $GLOBALS['SILENCE_LOG'])) {
            DB::dbLogEntry($sql); // Capture all queries in the ActivityLog
        }
        if (!$args) {
            return MyPDO::instance()->query($sql);
        }
        $stmt = MyPDO::instance()->prepare($sql);
        $stmt->execute($args);
        return $stmt;

    }


    public static function instance()
    {
        return MyPDO::instance();

    }


   /* end */
}


function lookup_cycleID($date)
{
    if ($date instanceof DateTime) {
        $year  = date("Y-m-d", $date);
    } else {
        $time = strtotime($date);
        $year  = date("Y-m-d", $time);
    }
    $sql = "SELECT AnnualCycleID FROM AnnualCycles WHERE DateTo >= '$year' AND DateFrom <= '$year';";
    $result = DB::run($sql);
    $value = $result->fetch();
    if ($value) {
        $cycle = $value['AnnualCycleID'];
        return $cycle;
    }
    return null;

}


$_currentCycle = null;
$_currentEvent = null;
$_currentEventName = null;


function current_cycleID()
{
    global $_currentCycle, $_SESSION;

    if ($_currentCycle == null) {
        /* Place 1 - Look for it in the session */

        if (isset($_SESSION['CurrentCycle'])) {
            return $_SESSION['CurrentCycle'];
        }

        /* Place 2 - try to get it from the database */

        $sql = "SELECT Value FROM Configuration WHERE Field='CurrentCycleID';";
        $result = DB::run($sql);
        $value = $result->fetch();
        if ($value) {
            $_currentCycle = $value['CurrentCycleID'];
            return $_currentCycle;
        }

        /* Place 3 - current calander year */

        $year = date("Y-m-d");
        $sql = "SELECT AnnualCycleID FROM AnnualCycles WHERE DateTo > '$year';";
        $result = DB::run($sql);
        $value = $result->fetch();
        if ($value) {
            $_currentCycle = $value['AnnualCycleID'];
            return $_currentCycle;
        }

        /* Fallback - Last year in database*/

        $sql = "SELECT AnnualCycleID FROM AnnualCycles ORDER BY AnnualCycleID DESC LIMIT 1;";
        $result = DB::run($sql);
        $value = $result->fetch();
        if ($value) {
            $_currentCycle = $value['AnnualCycleID'];
            return $_currentCycle;
        }

        return null;
    }
    return $_currentCycle;

}


function current_eventID()
{
    global $_currentEvent, $_SESSION;

    if ($_currentEvent == null) {
        /* Place 1 - Look for it in the session */

        if (isset($_SESSION['CurrentEvent'])) {
            return $_SESSION['CurrentEvent'];
        }

        /* Place 2 - try to get it from the database */

        $sql = "SELECT Value FROM Configuration WHERE Field='CurrentEventID';";
        $result = DB::run($sql);
        $value = $result->fetch();
        if ($value) {
            $_currentEvent = $value['CurrentEventID'];
            return $_currentEvent;
        }

        /* Place 3 - current calander year */

        $year = date("Y-m-d");
        $sql = "SELECT EventID FROM Events WHERE DateTo >= '$year';";
        $result = DB::run($sql);
        $value = $result->fetch();
        if ($value) {
            $_currentEvent = $value['EventID'];
            return $_currentEvent;
        }

        /* Fallback - Last year in database*/

        $sql = "SELECT EventID FROM Events ORDER BY EventID DESC LIMIT 1;";
        $result = DB::run($sql);
        $value = $result->fetch();
        if ($value) {
            $_currentEvent = $value['EventID'];
            return $_currentEvent;
        }

        return null;
    }
    return $_currentEvent;

}


function current_eventName()
{
    global $_currentEventName, $CONHOST;

    if ($_currentEventName === null) {
        $event = current_eventID();
        $sql = "SELECT `EventName` FROM Events WHERE EventID=$event;";
        try {
            $result = DB::run($sql);
            $value = $result->fetch();
            if ($value) {
                $_currentEventName = $value['EventName'];
                return $_currentEventName;
            } else {
                return $CONHOST;
            }
        } catch (PDOException $e) {
            return $CONHOST;
        }
    }
    return $_currentEventName;

}


function lookup_names(&$data, $column_name)
{
    /* HACK Lookup AccountIDs */
    if (in_array($column_name, $data[0])) {
        $data[0][] = $column_name.'Name';
        $colmn = array_search($column_name, $data[0]);
        $ids = array_column($data, $colmn);
        array_shift($ids);
        $users = lookup_users_by_ids(array_unique($ids));
        $usr = [];
        foreach ($users['users'] as $user) {
            $usr[$user['Id']] = $user['First Name'].' '.$user['Last Name'];
        }
        $idx = 1;
        foreach ($data as $id => $row) {
            $data[$idx][] = $usr[$data[$idx][$colmn]];
            $idx += 1;
        }
    }

}


function dump_sql_to_csv($sql, $lookup_account = false, $filter = null)
{
    $data = [];
    $data[] = array();
    $result = DB::run($sql);
    for ($i = 0; $i < $result->columnCount(); $i++) {
        $data[0][] = $result->getColumnMeta($i)['name'];
    }
    $value = $result->fetch();
    while ($value !== false) {
        $data[] = array_values($value);
        $value = $result->fetch();
    }

    if ($lookup_account) {
        lookup_names($data, 'AccountID');
        lookup_names($data, 'AuthorizedByID');
        lookup_names($data, 'EnteredByID');
    }

    $fh = fopen('php://output', 'w');
    ob_start();

    foreach ($data as $line) {
        if ($filter !== null) {
            $replace = '\1<REDACTED>\3';
            fputcsv($fh, preg_replace($filter, $replace, $line));
        } else {
            fputcsv($fh, $line);
        }
    }
    $text = ob_get_clean();

    return $text;

}


function dump_table_to_csv($table, $condition = null, $filter = null)
{
    $sql = "SELECT * FROM `$table` $condition;";
    return dump_sql_to_csv($sql, false, $filter);

}


function load_table($sql, $association = null)
{
    $result = DB::run($sql);
    $value = $result->fetch();
    $data = array();
    while ($value !== false) {
        if ($association !== null) {
            $entry = [];
            foreach ($association as $key => $dbkey) {
                $entry[$key] = $value[$dbkey];
            }
            $data[] = $entry;
        } else {
            $data[] = $value;
        }
        $value = $result->fetch();
    }
    return $data;

}
