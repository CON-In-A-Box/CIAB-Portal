<?php declare(strict_types=1);

/**
 * @OA\Delete(
 *     tags={"departments"},
 *     path="/department/{id}/permission/{permissionId}",
 *     summary="Delete department permission",
 *     @OA\Parameter(
 *         description="The department ID or 'all'",
 *         in="path",
 *         name="id",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         description="The permission ID",
 *         in="path",
 *         name="permissionId",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="OK"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         ref="#/components/responses/401"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         ref="#/components/responses/400"
 *     ),
 *     security={{"ciab_auth":{}}}
 * )
 */
namespace App\Controller\Department;

use App\Error\InvalidParameterException;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

use App\Service\PermissionService;

class DeleteDepartmentPermission extends BaseDepartment
{

    /**
     * @var PermissionService
     */
    protected $permissionService;


    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->permissionService = $container->get("PermissionService");
        $this->includes = null;

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        if ($params["id"] != "all" && intval($params["id"]) <= 0) {
            throw new InvalidParameterException("DepartmentID must be either 'all' or greater than 0.");
        }

        if (intval($params["permissionId"]) <= 0) {
            throw new InvalidParameterException("PermissionID must be greater than 0");
        }

        $this->permissionService->deleteById($params["permissionId"]);

        return [
          \App\Controller\BaseController::RESOURCE_TYPE,
          [null],
          204
        ];

    }


    /* End PostDepartmentPermission */
}
