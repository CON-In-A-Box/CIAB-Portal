<?php
class DB
{
    protected static $instance = null;

    protected function __construct() {}
    protected function __clone() {}

    public static function instance()
    {
        if (self::$instance === null)
        {
            $opt  = array(
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES   => TRUE,
            );
            $charset = 'utf8';
            $dsn = 'mysql:host='.$GLOBALS['DBHOST'];
            $dsn .= ';dbname='.$GLOBALS['DBNAME'];
            $dsn .= ';charset='.$charset;
            self::$instance = new PDO($dsn, $GLOBALS['DBUSER'], $GLOBALS['DBPASS'], $opt);
        }
        return self::$instance;
    }

    public static function __callStatic($method, $args)
    {
        return call_user_func_array(array(self::instance(), $method), $args);
    }

    public static function run($sql, $args = [])
    {
        if (!$args)
        {
             return self::instance()->query($sql);
        }
        $stmt = self::instance()->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }
    
    public function buildDB()
    {
        $sql = file_get_contents($DATADIR.'/init.sql');
        self::run($sql);
        
        sendError("Initalized ".$GLOBALS['DB_NAME']." Database.");
    }
}
?>
