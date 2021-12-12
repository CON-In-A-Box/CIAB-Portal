<?php

/*.
    require_module 'standard';
.*/

namespace ciab;

class RBAC
{

    protected static $instance = null;

    protected static $custom = array();


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
            if (!empty(self::$custom)) {
                if (self::instance() !== null) {
                    foreach (self::$custom as $entry) {
                        return self::instance()->customizeRBAC($entry);
                    }
                }
            }
        }

    }


    public static function havePermission(/*.string.*/ $name)
    {
        if (self::instance() !== null) {
            return self::instance()->havePermission($name);
        } else {
            return (array_key_exists('IS_ADMIN', $_SESSION) &&
                    $_SESSION['IS_ADMIN']);
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


    public static function customizeRBAC($entry)
    {
        if (self::instance() !== null) {
            self::$custom[] = $entry;
            return self::instance()->customizeRBAC($entry);
        }

    }


    /* end class */
}
