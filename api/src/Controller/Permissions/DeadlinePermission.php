<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Permissions;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class DeadlinePermission extends BasePermission
{


    const ALL_METHODS = ['get', 'put', 'post', 'delete'];


    public function __construct(Container $container)
    {
        parent::__construct($container);
        \ciab\RBAC::customizeRBAC(array($this, 'customizeDeadlineRBAC'));

    }


    public function processIncludes(Request $request, Response $response, $args, $values, &$data)
    {
        if (in_array('departmentId', $values)) {
            $target = new \App\Controller\Department\GetDepartment($this->container);
            $newargs = $args;
            $newargs['name'] = $data['subdata']['departmentId'];
            $newdata = $target->buildResource($request, $response, $newargs)[1];
            if ($newdata['id'] != $data['id']) {
                $target->processIncludes($request, $response, $args, $values, $newdata);
                $data['subdata']['departmentId'] = $target->arrayResponse($request, $response, $newdata);
            }
        }

    }


    protected function buildDeptEntry($id, $allowed, $subtype, $method, $hateoas)
    {
        $entry = $this->buildBaseEntry($allowed, $subtype, $method, $hateoas);
        $entry['subdata'] = [
        'departmentId' => $id
        ];
        return $entry;

    }


    public function customizeDeadlineRBAC($instance)
    {
        $positions = [];
        $sql = "SELECT `PositionID`, `Name` FROM `ConComPositions` ORDER BY `PositionID` ASC";
        $result = $this->container->db->prepare($sql);
        $result->execute();
        $value = $result->fetch();
        while ($value !== false) {
            $positions[intval($value['PositionID'])] = $value['Name'];
            $value = $result->fetch();
        }

        $result = $this->container->db->prepare("SELECT `DepartmentID` FROM `Departments`");
        $result->execute();
        $value = $result->fetch();
        while ($value !== false) {
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
            $value = $result->fetch();
        }

    }


    /* end DeadlinePermission */
}
