<?php

/*.
    require_module 'standard';
.*/

namespace App\Handler;

#require_once __DIR__.'/../../../modules/concom/functions/RBAC.inc';

class RBAC
{

    protected static $instance = null;

    protected static $custom = array();


    public function __construct()
    {

    }


    public function __clone()
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


    public static function getMemberPermissions(
        /*.int.*/ $id
    ) {
        if (self::instance() !== null) {
            return self::instance()->getMemberPermissions($id);
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


    public static function registerPermissions(/*.mixed.*/$permissions)
    {
        if (self::instance() !== null) {
            return self::instance()->registerPermissions($permissions);
        }

    }


    public static function addRole(/*.string.*/$role, /*.array.*/$parents = null)
    {
        if (self::instance() !== null) {
            return self::instance()->addRole($role, $parents);
        }

    }


    public static function removeRole(/*.string.*/$role)
    {
        if (self::instance() !== null) {
            return self::instance()->removeRole($role);
        }

    }


    public static function addRoleParents(/*.string.*/$role, /*.array.*/$parents)
    {
        if (self::instance() !== null) {
            return self::instance()->addRoleParents($role, $parents);
        }

    }


    public static function removeRoleParent(/*.string.*/$role, /*.string.*/$parent)
    {
        if (self::instance() !== null) {
            return self::instance()->removeRoleParent($role, $parent);
        }

    }


    public static function grantPermission(/*.string.*/$role, /*.string.*/$permission)
    {
        if (self::instance() !== null) {
            return self::instance()->grantPermission($role, $permission);
        }

    }


    public static function revokePermission(/*.string.*/$role, /*.string.*/$permission)
    {
        if (self::instance() !== null) {
            return self::instance()->revokePermission($role, $permission);
        }

    }


    /* end class */
}
