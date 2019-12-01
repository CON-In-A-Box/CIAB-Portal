<?php
/*
 */

/*.
    require_module 'standard';
  .*/

namespace ciab\backend;

class Asset
{


    public function __construct()
    {

    }


    public function configuration()
    {
        return array();

    }


    public static function load(/*.string.*/ $id)
    {
        error_log("No asset manager configured. Unable to load asset('$id')");
        return "";

    }


    public static function save(/*.string.*/ $id, /*.mixed.*/ $data)
    {
        error_log("No asset manager configured. Unable to save asset('$id')");

    }


    public function exists(/*.string.*/ $id)
    {
        return false;

    }


    /* end class */
}
