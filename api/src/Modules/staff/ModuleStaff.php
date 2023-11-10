<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\staff;

use App\Modules\BaseModule;
use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;
use App\Modules\staff\Database\StaffDBSchema;

class ModuleStaff extends BaseModule
{


    public function __construct($source)
    {
        parent::__construct($source);

    }


    public function install($container)
    {
        $container->RBAC->customizeRBAC('\App\Modules\staff\ModuleStaff::customzieStaffRBAC');

    }


    public function databaseInstall($container)
    {
        $db = new StaffDBSchema($container->db);
        $db->update(true);

    }


    public function valid()
    {
        if ($this->source !== null) {
            if (get_class($this->source) === 'App\Controller\Member\GetMember' ||
                get_class($this->source) === 'App\Controller\Department\GetDepartment') {
                return true;
            }
        }
        return false;

    }


    public function handle(Request $request, Response $response, $data, $code)
    {
        return $data;

    }


    public function customzieStaffRBAC($rbac, $database)
    {
        $rbac->grantPermission('all.staff', 'api.get.staff');

        $positions = [];
        $values = Select::new($database)
            ->columns('PositionID', 'Name')
            ->from('ConComPositions')
            ->orderBy('`PositionID` ASC')
            ->fetchAll();

        foreach ($values as $value) {
            $positions[intval($value['PositionID'])] = $value['Name'];
        }

        $values = Select::new($database)
            ->columns('DepartmentID', 'ParentDepartmentID', 'Name')
            ->from('Departments')
            ->fetchAll();

        foreach ($values as $value) {
            $parentDept = $value['ParentDepartmentID'];
            $dept = $value['DepartmentID'];

            if ($parentDept != $dept) {
                // Grants for Dept Head - They can add members to their department
                $deptHeadPos = reset(array_keys($positions));
                $deptHeadRole = "$dept.$deptHeadPos";
                self::grantPermission($rbac, $deptHeadRole, "api.post.staff.$dept");

                // Grants for Division Staff - They can add to any department within their division, and they can edit any department members within their division.
                foreach ($positions as $directorPosId => $directorPos) {
                    $targetRole = "$parentDept.$directorPosId";
                    self::grantPermission($rbac, $targetRole, "api.post.staff.$dept");

                    foreach ($positions as $deptPosId => $deptPos) {
                        self::grantPermission($rbac, $targetRole, "api.put.staff.$dept.$deptPosId");
                        self::grantPermission($rbac, $targetRole, "api.delete.staff.$dept.$deptPosId");
                    }
                }
            }

            // Grants for Division Director to edit members within their Division for positions lower than them
            // Grants for Department Head to edit members within their Department for positions lower than them
            $topPos = reset(array_keys($positions));
            foreach ($positions as $id => $position) {
                $currId = intval($id) + 1;
                if (array_key_exists($currId, $positions)) {
                    $targetRole = "$dept.$topPos";
                    self::grantPermission($rbac, $targetRole, "api.put.staff.$dept.$currId");
                    self::grantPermission($rbac, $targetRole, "api.delete.staff.$dept.$currId");
                }
            }
        }

    }


    private function grantPermission($rbac, $targetRole, $permission)
    {
        try {
            $rbac->grantPermission($targetRole, $permission);
        } catch (Exception\InvalidArgumentException $e) {
            error_log($e);
        }

    }


    /* End ModuleStaff */
}
