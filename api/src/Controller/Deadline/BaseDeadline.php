<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Tag(
 *      name="deadlines",
 *      description="Features around event department deadlines"
 *  )
 *
 *  @OA\Schema(
 *      schema="deadline",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"deadline"}
 *      ),
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          description="Deadline Id"
 *      ),
 *      @OA\Property(
 *          property="deadline",
 *          type="string",
 *          format="date",
 *          description="When this deadline expires"
 *      ),
 *      @OA\Property(
 *          property="department",
 *          description="Department for the deadline",
 *          oneOf={
 *              @OA\Schema(
 *                  ref="#/components/schemas/department"
 *              ),
 *              @OA\Schema(
 *                  type="integer",
 *                  description="Department Id"
 *              )
 *          }
 *      ),
 *      @OA\Property(
 *          property="note",
 *          type="string",
 *          description="Note about the deadline."
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="deadline_list",
 *      allOf = {
 *          @OA\Schema(ref="#/components/schemas/resource_list")
 *      },
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"deadline_list"}
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="List of deadlines",
 *          @OA\Items(
 *              ref="#/components/schemas/deadline"
 *          ),
 *      )
 *  )
 *
 *  @OA\Response(
 *      response="deadline_not_found",
 *      description="Deadline not found in the system.",
 *      @OA\JsonContent(
 *          ref="#/components/schemas/error"
 *      )
 *   )
 **/

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
        $output = array();
        $output['type'] = 'deadline';
        $output['id'] = $id;
        $output['department'] = $dept;
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


    /* End BaseDeadline */
}
