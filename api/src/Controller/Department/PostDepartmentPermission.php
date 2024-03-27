<?php declare(strict_types=1);

/**
 * @OA\Post(
 *     tags={"departments"},
 *     path="/department/{id}/permission",
 *     summary="Create new department permission",
 *     @OA\Parameter(
 *         description="The department ID or 'all'",
 *         in="path",
 *         name="id",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="PositionID",
 *                     description="The position ID for the permission",
 *                     nullable=false,
 *                     type="integer"
 *                 ),
 *                 @OA\Property(
 *                     property="Permission",
 *                     description="The name of the permission",
 *                     nullable=false,
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="Note",
 *                     description="The note for the permission",
 *                     nullable=true,
 *                     type="string"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="OK",
 *         @OA\JsonContent(
 *             ref="#/components/schemas/department_permission"
 *         )
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

class PostDepartmentPermission extends BaseDepartment
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
      
        $required = ['PositionID', 'Permission'];
        $body = $this->checkRequiredBody($request, $required);

        if (strlen(trim($body["Permission"])) == 0) {
            throw new InvalidParameterException("Permission must be a non-empty string");
        }

        if (intval($body["PositionID"]) <= 0) {
            throw new InvalidParameterException("PositionID must be greater than 0");
        }

        $permissionArgs = [
          "Position" => $params["id"].".".$body["PositionID"],
          "Permission" => $body["Permission"]
        ];

        $createdPermission = $this->permissionService->post($permissionArgs);
        $result = $this->permissionService->getById($createdPermission);

        return [
          \App\Controller\BaseController::RESOURCE_TYPE,
          $result,
          201
        ];

    }


    /* End PostDepartmentPermission */
}
