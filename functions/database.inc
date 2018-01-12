<?php

// Currently, this database class is set for the MySQL PDO filter/backend. Future version will expand it to be agnostic

require_once($BACKEND.'/'.$DB_BACKEND); // MyPDO Definition used
//$mypdo = new MyPDO();

class DB
{
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
