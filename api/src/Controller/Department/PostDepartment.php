<?php declare(strict_types=1);

/**
 * @OA\Post(
 *     tags={"departments"},
 *     path="/department",
 *     summary="Create new department",
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="Name",
 *                     description="The name for the new department",
 *                     nullable=false,
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="ParentID",
 *                     description="The ID for the parent department",
 *                     nullable=true,
 *                     type="integer"
 *                 ),
 *                 @OA\Property(
 *                     property="FallbackID",
 *                     description="The ID for the fallback department",
 *                     nullable=true,
 *                     type="integer"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="OK",
 *         @OA\JsonContent(
 *             ref="#/components/schemas/department"
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

use App\Service\DepartmentService;

class PostDepartment extends BaseDepartment
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
        // Need to figure out which permission is appropriate here.
        // $permissions = ['site.concom.permissions'];
        // $this->checkPermissions($permissions);

        $required = ['Name'];
        $body = $this->checkRequiredBody($request, $required);

        if (array_key_exists("ParentID", $body) && $body["ParentID"] <= 0) {
            throw new InvalidParameterException("ParentID must be greater than 0");
        }

        if (array_key_exists("FallbackID", $body) && $body["FallbackID"] <= 0) {
            throw new InvalidParameterException("FallbackID must be greater than 0");
        }

        $createdDept = $this->departmentService->post($body);
        $result = $this->departmentService->getById($createdDept)[$createdDept];

        return [
          \App\Controller\BaseController::RESOURCE_TYPE,
          $result,
          201
        ];

    }


  /* End PostDepartment */
}
