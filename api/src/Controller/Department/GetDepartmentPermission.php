<?php declare(strict_types=1);

/**
 * @OA\Get(
 *     tags={"departments"},
 *     path="/department/{id}/permission",
 *     summary="List department permissions",
 *     @OA\Parameter(
 *         description="The department ID",
 *         in="path",
 *         name="id",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="OK",
 *         @OA\JsonContent(
 *             ref="#/components/schemas/department_permission_list"
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         ref="#/components/responses/401"
 *     ),
 *     security={{"ciab_auth":{}}}
 * )
 */
namespace App\Controller\Department;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

use App\Service\DepartmentService;

class GetDepartmentPermission extends BaseDepartment
{

    /**
     * @var DepartmentService
     */
    protected $departmentService;


    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->departmentService = $container->get("DepartmentService");
        $this->includes = null;

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $output = $this->departmentService->listPermissionsByDepartment($params["id"]);
        return [
          \App\Controller\BaseController::LIST_TYPE,
          $output,
          array('type' => 'department_permission_list')
        ];

    }


    /* end GetDepartmentPermission */
}
