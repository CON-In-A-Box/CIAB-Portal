<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Deadline;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\BaseController;

abstract class BaseDeadline extends BaseController
{


    public function __construct(Container $container)
    {
        parent::__construct('deadline', $container);
        \ciab\RBAC::customizeRBAC(array($this, 'customizeDeadlineRBAC'));

    }


    public function buildDeadline(Request $request, Response $response, $id, $dept, $deadline, $note)
    {
        $this->buildDeadlineHateoas($request, intval($id), intval($dept));
        $output = array();
        $output['type'] = 'deadline';
        $output['id'] = $id;
        $output['departmentId'] = $dept;
        $output['deadline'] = $deadline;
        $output['note'] = $note;
        return $output;

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


    protected function buildDeadlineHateoas(Request $request, int $id, int $dept)
    {
        if ($id !== 0) {
            $path = $request->getUri()->getBaseUrl();
            $this->addHateoasLink('self', $path.'/deadline/'.strval($id), 'GET');
            $this->addHateoasLink('modify', $path.'/deadline/'.strval($id), 'POST');
            $this->addHateoasLink('delete', $path.'/deadline/'.strval($id), 'DELETE');
            $this->addHateoasLink('department', $path.'/department/'.strval($dept), 'GET');
        }

    }


    /* End BaseDeadline */
}
