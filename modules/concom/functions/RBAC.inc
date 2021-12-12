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


    protected function __construct()
    {

    }


    protected function __clone()
    {

    }


    private static function addToAll(
        /*.Rbac.*/$rbac,
        /*.Role.*/$staff,
        /*.string.*/$new_role,
        /*.string.*/$pos
    ) {
        $role = $rbac->rbac->getRole($new_role);
        $staff->addParent($role);
        $target = $rbac->rbac->getRole("all.$pos");
        $target->addParent($role);

    }


    public static function instance()
    {
        if (self::$instance === null) {
            $rbac = new ConComRBAC();
            $rbac->rbac = new Rbac();

            $root = new Role('admin');
            $rbac->rbac->addRole($root);

            $staff = new Role('all.staff');
            $rbac->rbac->addRole($staff, $root);

            $positions = [];
            $sql = "SELECT `PositionID` FROM `ConComPositions` ORDER BY `PositionID` ASC";
            $result = \DB::run($sql);
            $value = $result->fetch();
            $parent = 'admin';
            while ($value !== false) {
                $positions[] = $value['PositionID'];
                $new = 'all.'.$value['PositionID'];
                $rbac->rbac->addRole($new, $parent);
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
                    $rbac->rbac->addRole($new, $parent);
                    self::addToAll($rbac, $staff, $new, $pos);
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
                    $rbac->rbac->addRole($new, $parent);
                    self::addToAll($rbac, $staff, $new, $pos);
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
                    $role = $rbac->rbac->getRole(strval($value['Position']));
                    $role->addPermission($value['Permission']);
                } catch (Exception\InvalidArgumentException $e) {
                }
                $value = $result->fetch();
            }

            self::$instance = $rbac;
        }

        return self::$instance;

    }


    protected static function positionHasPermission(
        /*.mixed.*/$pos,
        /*.string.*/$name
    ) {
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


    public static function customizeRBAC($entry)
    {
        try {
            @\call_user_func($entry, self::instance()->rbac, \DB::instance());
        } catch (\Exception $e) {
            error_log($e);
        }

    }


    /* end RBAC */
}
