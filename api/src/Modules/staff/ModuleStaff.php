<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\staff;

use App\Modules\BaseModule;
use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;

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
            ->columns('PositionID')
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
                $targetRole = "$parentDept.".end(array_keys($positions));
                try {
                    $rbac->grantPermission($targetRole, "api.put.staff.$dept.1");
                    $rbac->grantPermission($targetRole, "api.delete.staff.$dept.1");
                } catch (Exception\InvalidArgumentException $e) {
                    error_log($e);
                }
            }

            $idx = array_keys($positions)[0];
            try {
                $targetRole = "$dept.$idx";
                $rbac->grantPermission($targetRole, "api.post.staff.$dept");
            } catch (Exception\InvalidArgumentException $e) {
                error_log($e);
            }

            foreach ($positions as $id => $position) {
                $currId = intval($id) + 1;
                if (array_key_exists($currId, $positions)) {
                    $targetRole = "$dept.$idx";
                    try {
                        $rbac->grantPermission($targetRole, "api.put.staff.$dept.$currId");
                        $rbac->grantPermission($targetRole, "api.delete.staff.$dept.$currId");
                    } catch (Exception\InvalidArgumentException $e) {
                        error_log($e);
                    }
                }
            }
        }

    }


    /* End ModuleStaff */
}
