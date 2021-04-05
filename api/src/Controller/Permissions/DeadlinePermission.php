<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Permissions;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;

abstract class DeadlinePermission extends BasePermission
{


    public function __construct(Container $container)
    {
        parent::__construct($container, 'deadline', ['get', 'put', 'post', 'delete']);
        \ciab\RBAC::customizeRBAC(array($this, 'customizeDeadlineRBAC'));

    }


    public function customizeDeadlineRBAC($instance)
    {
        $positions = [];
        $select = Select::new($this->container->db);
        $select->columns('PositionID', 'Name')->from('ConComPositions')->orderBy('`PositionID` ASC');
        $values = $select->fetchAll();
        foreach ($values as $value) {
            $positions[intval($value['PositionID'])] = $value['Name'];
        }

        $select = Select::new($this->container->db);
        $select->columns('DepartmentID')->from('Departments');
        $values = $select->fetchAll();
        foreach ($values as $value) {
            $perm_get = 'api.get.deadline.'.$value['DepartmentID'];
            $perm_del = 'api.delete.deadline.'.$value['DepartmentID'];
            $perm_pos = 'api.post.deadline.'.$value['DepartmentID'];
            $perm_put = 'api.put.deadline.'.$value['DepartmentID'];
            $target_l = $value['DepartmentID'].'.'.end(array_keys($positions));
            $target_h = $value['DepartmentID'].'.'.array_keys($positions)[0];
            try {
                $role = $instance->getRole($target_l);
                $role->addPermission($perm_get);
                $role = $instance->getRole($target_h);
                $role->addPermission($perm_del);
                $role->addPermission($perm_pos);
                $role->addPermission($perm_put);
            } catch (Exception\InvalidArgumentException $e) {
                error_log($e);
            }
        }

    }


    /* end DeadlinePermission */
}
