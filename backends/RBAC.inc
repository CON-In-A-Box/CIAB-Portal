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


    public static function reload()
    {
        if (self::$instance !== null) {
            self::$instance = null;
        }

    }


    public static function havePermission(/*.string.*/ $name)
    {
        if (self::instance() !== null) {
            return self::instance()->havePermission($name);
        } else {
            return ($_SESSION['IS_ADMIN']);
        }

    }


    public static function getPermissions(
        /*.string.*/ $role,
        /*.bool.*/ $children = true
    ) {
        if (self::instance() !== null) {
            return self::instance()->getPermissions($role, $children);
        } else {
            return null;
        }

    }


    /* end class */
}
