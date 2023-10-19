<?php

namespace concom;

/*.
    require_module 'standard';
  .*/

require __DIR__."/../../../vendor/autoload.php";
require_once(__DIR__."/POSITION.inc");

use Zend\Permissions\Rbac\Rbac;
use Zend\Permissions\Rbac\Role;

class ConComRBAC
{

    /**
     * @var ConComRBAC
     */
    protected static $instance = null;

    /**
     * @var Rbac
     */
    protected $rbac = null;

    /**
     * @var Permissions
     */
    protected $permissions = array();


    protected function __construct()
    {

    }


    protected function __clone()
    {

    }


    private static function addToAll(
        /*.string.*/$staff,
        /*.string.*/$new_role,
        /*.string.*/$pos
    ) {
        self::addRoleParents($staff, [$new_role]);
        self::addRoleParents("all.$pos", [$new_role]);

    }


    public static function instance()
    {
        if (self::$instance === null) {
            $rbac = new ConComRBAC();
            $rbac->rbac = new Rbac();
            self::$instance = $rbac;

            self::addRole('admin');
            self::addRole('all.staff', 'admin');

            $positions = [];
            $sql = "SELECT `PositionID` FROM `ConComPositions` ORDER BY `PositionID` ASC";
            $result = \DB::run($sql);
            $value = $result->fetch();
            $parent = 'admin';
            while ($value !== false) {
                $positions[] = $value['PositionID'];
                $new = 'all.'.$value['PositionID'];
                self::addRole($new, [$parent]);
                $parent = $new;
                $value = $result->fetch();
            }

            $sql = "SELECT * FROM `Departments` WHERE ParentDepartmentID = DepartmentID";
            $result = \DB::run($sql);
            $value = $result->fetch();
            while ($value !== false) {
                $parent = 'admin';
                foreach ($positions as $pos) {
                    $new = $value['DepartmentID'].'.'.$pos;
                    self::addRole($new, [$parent]);
                    self::addToAll('all.staff', $new, $pos);
                    $parent = $new;
                }
                $value = $result->fetch();
            }

            $sql = "SELECT * FROM `Departments` WHERE ParentDepartmentID != DepartmentID";
            $result = \DB::run($sql);
            $value = $result->fetch();
            while ($value !== false) {
                $firstparent = $value['ParentDepartmentID'].'.'.end($positions);
                $parent = $firstparent;
                foreach ($positions as $pos) {
                    $new = $value['DepartmentID'].'.'.$pos;
                    self::addRole($new, [$parent]);
                    self::addToAll('all.staff', $new, $pos);
                    if ($parent == $firstparent) {
                        $parent = $new;
                    }
                }
                $value = $result->fetch();
            }

            /* Load Permissions */
            $sql = "SELECT * FROM `ConComPermissions`";
            $result = \DB::run($sql);
            $value = $result->fetch();
            $retvalue = [];
            while ($value !== false) {
                try {
                    self::$instance->grantPermission(strval($value['Position']), $value['Permission']);
                } catch (Exception\InvalidArgumentException $e) {
                }
                if (!in_array($value['Permission'], self::$instance->permissions)) {
                    self::$instance->permissions[] = $value['Permission'];
                }
                $value = $result->fetch();
            }
        }

        return self::$instance;

    }


    protected static function positionHasPermission(
        /*.mixed.*/$pos,
        /*.string.*/$name
    ) {
        if (!in_array($name, self::$instance->permissions)) {
            error_log("Unregistered RBAC permission ".$name);
        }
        try {
            return self::instance()->rbac->getRole(
                strval($pos)
            )->hasPermission($name);
        } catch (Exception\InvalidArgumentException $e) {
            return false;
        }

    }


    public static function hasPermissions(
        /*.mixed.*/$accountId,
        /*.string.*/$name
    ) {
        if (!in_array($name, self::$instance->permissions)) {
            error_log("Unregistered RBAC permission ".$name);
        }
        if (array_key_exists('IS_ADMIN', $_SESSION) && $_SESSION['IS_ADMIN']) {
            return true;
        }
        $positions = POSITION::getConComPosition($accountId);
        foreach ($positions as $pos) {
            $value = $pos['departmentId'].'.'.$pos['positionId'];
            if (self::positionHasPermission($value, $name)) {
                return true;
            }
        }
        return false;

    }


    public static function havePermission(/*.string.*/$name)
    {
        if (!in_array($name, self::$instance->permissions)) {
            error_log("Unregistered RBAC permission ".$name);
        }
        return self::hasPermissions($_SESSION['accountId'], $name);

    }


    public static function getPermissions(
        /*.string.*/$role,
        /*.bool.*/ $children = true
    ) {
        try {
            $result = self::instance()->rbac->getRole(
                strval($role)
            )->getPermissions($children);
            return array_unique($result);
        } catch (Exception\InvalidArgumentException $e) {
            return null;
        }

    }


    public static function getMemberPermissions(
        /*.int.*/$accountId
    ) : array {
        $result = [];
        if (array_key_exists('IS_ADMIN', $_SESSION) && $_SESSION['IS_ADMIN']) {
            return self::$instance->permissions;
        }
        $positions = POSITION::getConComPosition($accountId);
        foreach ($positions as $pos) {
            $role = $pos['departmentId'].'.'.$pos['positionId'];
            try {
                $result = array_unique(array_merge($result, self::instance()->rbac->getRole(
                    strval($role)
                )->getPermissions(true)));
            } catch (Exception\InvalidArgumentException $e) {
            }
        }
        return $result;

    }


    public static function customizeRBAC($entry)
    {
        try {
            @\call_user_func($entry, self::instance(), \DB::instance());
        } catch (\Exception $e) {
            error_log($e);
        }

    }


    public static function registerPermissions(/*.mixed.*/$permissions)
    {
        if (is_array($permissions)) {
            foreach ($permissions as $p) {
                if (!in_array($p, self::$instance->permissions)) {
                    self::$instance->permissions[] = $p;
                }
            }
        } else {
            if (!in_array($permissions, self::$instance->permissions)) {
                self::$instance->permissions[] = $permissions;
            }
        }

    }


    public static function addRole(/*.string.*/$role, /*.array.*/$parents = null)
    {
        try {
            $r = new Role($role);
            self::instance()->rbac->addRole($r, $parents);
        } catch (\Exception $e) {
            error_log($e);
        }

    }


    public static function removeRole(/*.string.*/$role)
    {
        /* No action in Vend/Laminas backend */

    }


    public static function addRoleParents(/*.string.*/$role, /*.array.*/$parents)
    {
        try {
            $r = self::instance()->rbac->getRole($role);
            if (!$r) {
                self::addRole($role, $parents);
            } else {
                foreach($parents as $parent) {
                    $p = self::instance()->rbac->getRole($parent);
                    $r->addParent($p);
                }
            }
        } catch (\Exception $e) {
            error_log($e);
        }

    }


    public static function removeRoleParent(/*.string.*/$role, /*.string.*/$parent)
    {
        /* No action in Vend/Laminas backend */

    }


    public static function grantPermission(/*.string.*/$role, /*.string.*/$permission)
    {
        try {
            $r = self::instance()->rbac->getRole($role);
            if ($r) {
                $r->addPermission($permission);
            }
        } catch (\Exception $e) {
            error_log($e);
        }

    }


    public static function revokePermission(/*.string.*/$role, /*.string.*/$permission)
    {
        /* No action in Vend/Laminas backend */

    }


    /* end RBAC */
}
