<?php

/*.
    require_module 'standard';
.*/

namespace ciab;

class RBAC
{

    protected static $instance = null;


    protected function __construct()
    {

    }


    protected function __clone()
    {

    }


    public static function instance()
    {
        if (self::$instance === null) {
            if (class_exists('\\concom\\ConComRBAC')) {
                self::$instance = \concom\ConComRBAC::instance();
            }
        }

        return self::$instance;

    }


    public static function havePermission($what)
    {
        if (self::instance() !== null) {
            return self::instance()->havePermission($what);
        } else {
            return ($_SESSION['IS_ADMIN']);
        }
    }

    /* end class */
}
