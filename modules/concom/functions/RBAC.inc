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

    public static function instance()
    {
        if (self::$instance === null) {
            $rbac = new ConComRBAC();
            $rbac->rbac = new Rbac();

            $root = new Role('admin');
            $rbac->rbac->addRole($root);

            $positions = [];
            $sql = "SELECT `PositionID` FROM `ConComPositions` ORDER BY `PositionID` ASC";
            $result = \DB::run($sql);
            $value = $result->fetch();
            while ($value !== false) {
                $positions[] = $value['PositionID'];
                $value = $result->fetch();
            }

            $sql = "SELECT * FROM `Departments` WHERE ParentDepartmentID = DepartmentID";
            $result = \DB::run($sql);
            $value = $result->fetch();
            while ($value !== false) {
                $parent = 'admin';
                foreach($positions as $pos) {
                    $new = $value['DepartmentID'].'.'.$pos;
                    $rbac->rbac->addRole($new, $parent);
                    $parent = $new;
                }
                $value = $result->fetch();
            }

            $sql = "SELECT * FROM `Departments` WHERE ParentDepartmentID != DepartmentID";
            $result = \DB::run($sql);
            $value = $result->fetch();
            while ($value !== false) {
                $parent = $value['ParentDepartmentID'].'.'.end($positions);
                foreach($positions as $pos) {
                    $new = $value['DepartmentID'].'.'.$pos;
                    $rbac->rbac->addRole($new, $parent);
                    $parent = $new;
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

    protected static function positionHasPermission($pos, $what)
    {
        try {
            return self::instance()->rbac->getRole(
                strval($pos))->hasPermission($what);
        } catch (Exception\InvalidArgumentException $e) {
            return false;
        }
    }

    public static function hasPermissions($accountId, $what)
    {
        if ($_SESSION['IS_ADMIN']) {
            return true;
        }
        $positions = POSITION::getConComPosition('accountId');
        foreach($positions as $pos) {
            $value = $pos['departmentId'].'.'.$pos['positionId'];
            if (self::positionHasPermission($value, $what))
                return true;
        }
        return false;
    }

    public static function havePermission($what)
    {
        return self::hasPermissions($_SESSION['accountId'], $what);
    }

    /* end RBAC */
}
