<?php

/*
 * Interface to the backend asset manager
 */
/*.
    require_module 'standard';
.*/

namespace ciab;

class Asset
{

    protected static /*.Asset.*/ $instance = null;


    protected function __construct()
    {

    }


    protected function __clone()
    {

    }


    public static function instance()
    {
        if (self::$instance === null) {
            $client = $GLOBALS['ASSET_BACKEND'];
            if (!$client || empty($client)) {
                $client = 'file.inc';
            }
            $client = __DIR__."/asset/".$client;
            if (!file_exists($client)) {
                error_log("CIAB ERROR: "."Requested asset manager does not exist! "."Unable to load assets! Please correct settings!");
                $client = __DIR__."/asset/null.inc";
            }

            include_once($client);

            self::$instance = new backend\Asset();
        }

        return self::$instance;

    }


    public static function configuration()
    {
        $config = self::instance()->configuration();
        $client = $GLOBALS['ASSET_BACKEND'];
        if (!$client || empty($client)) {
            $client = 'file.inc';
        }
        $backends = self::backends();
        $config['ASSET_BACKEND'] = [
        'description' => 'Backend for Asset Library ( '.implode(', ', $backends).' )',
        'value' => $client,
        'readonly' => false
        ];
        ksort($config);
        return $config;

    }


    public static function backends()
    {
        $backends = array();
        $files = scandir(__DIR__.'/asset/');
        foreach ($files as $file) {
            if ($file[0] !== '.' && $file !== 'null.inc') {
                $backends[] = $file;
            }
        }
        return $backends;

    }


    public static function load(/*.string.*/ $id)
    {
        return self::instance()->load($id);

    }


    public static function save(/*.string.*/ $id, /*.string.*/ $data)
    {
        return self::instance()->save($id, $data);

    }


    public static function exists(/*.string.*/ $id)
    {
        return self::instance()->exists($id);

    }


    /* end class */
}
