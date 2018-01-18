<?php

// Currently, this database class is set for the MySQL PDO filter/backend. Future version will expand it to be agnostic

require_once($BACKEND.'/'.$DB_BACKEND); // MyPDO Definition used

class DB
{
    public function __construct()
    {
        global $DATADIR;
        require_once($DATADIR.'/database-schema.php');
        
        $result = MyPDO::instance()->query("SHOW TABLES");
        $arr = [];
        foreach($result as $value) {
            foreach($value as $row) {
                $arr[] = $row;
            }
        }

        foreach($DB_tables as $table => $fields) {
            if(!in_array($table, $arr)) {
                // Missing a table, build it!
                $build = 'CREATE TABLE '.$table.' (';
                foreach($fields as $column => $setting) {
                   $build .= $column.' '.$setting.', ';
                }
                $build = rtrim($build,', '). ");";

                MyPDO::instance()->query($build);
                sendError("Initalized ".$table." schema.");

                // If there is seed available for this table, seed it!
                if(file_exists($DATADIR.'/DBSeed/'.$table.'.sql')) {
                    $sql_data=file_get_contents($DATADIR.'/DBSeed/'.$table.'.sql');
                    MyPDO::instance()->query($sql_data);
                    sendError("Seeded ".$table." table data.");
                }
            }
        }
    }

    public static function run($sql, $args = [])
    {
        if (!$args)
        {
             return MyPDO::instance()->query($sql);
        }
        $stmt = MyPDO::instance()->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }
}
?>
