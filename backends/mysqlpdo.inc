<?php

// Abstraction Layer for MySQL via PDO

class MyPDO
{
    protected static $instance = null;

    protected function __construct() {}
    protected function __clone() {}
    
    public static function instance()
    {
        if (self::$instance === null)
        {
            // Setup a DB connecition via the parent PDO (In this case, MySQL Specific)
            $options = [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ];
            $username = $GLOBALS['DBUSER']; // Pulled from Config file
            $password = $GLOBALS['DBPASS']; // Pulled from Config file
            $charset = 'utf8'; // Default, Future need update for locale?
            $dsn = 'mysql:';
            $dns .= 'host='.$GLOBALS['DBHOST']; // Pulled from Config file
            $dsn .= ';dbname='.$GLOBALS['DBNAME']; // Pulled from Config file
            $dsn .= ';charset='.$charset;
            self::$instance = new PDO($dsn, $username, $password, $options);
        }
        return self::$instance;
    }
    
    public static function __callStatic($method, $args)
    {
        return call_user_func_array(array(self::instance(), $method), $args);
    }
}
?>
