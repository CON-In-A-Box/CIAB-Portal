<?php declare(strict_types=1);

/**
 * @OA\Put(
 *     tags={"departments"},
 *     path="/department/{id}",
 *     summary="Update existing department",
  *    @OA\Parameter(
 *         description="ID of department being updated",
 *         in="path",
 *         name="id",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="Name",
 *                     description="The name of the department",
 *                     nullable=true,
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
 *         response=200,
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

class PutDepartment extends BaseDepartment
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
        $departmentId = $params["id"];
        if ($departmentId <= 0) {
            throw new InvalidParameterException("DepartmentID must be greater than 0");
        }

        $body = $this->validateRequestParams($request);

        $this->departmentService->put($departmentId, $body);
        $result = $this->departmentService->getById($departmentId)[$departmentId];

        return [
          \App\Controller\BaseController::RESOURCE_TYPE,
          $result,
          200
        ];

    }


    private function validateRequestParams(Request $request): array
    {
        $body = $request->getParsedBody();

        if ($body == null || count($body) == 0) {
            throw new InvalidParameterException("Must include at least one of 'Name', 'ParentID', or 'FallbackID'");
        }

        $hasOneValidField = array_key_exists("Name", $body) || array_key_exists("ParentID", $body) || array_key_exists("FallbackID", $body);
        if (!$hasOneValidField) {
            throw new InvalidParameterException("Must include at least one of 'Name', 'ParentID', or 'FallbackID'");
        }

        if (array_key_exists("Name", $body) && strlen(trim($body["Name"])) == 0) {
            throw new InvalidParameterException("Name must be a non-empty string");
        }

        if (array_key_exists("ParentID", $body) && (!is_numeric($body["ParentID"]) || $body["ParentID"] <= 0)) {
            throw new InvalidParameterException("ParentID must be greater than 0");
        }

        if (array_key_exists("FallbackID", $body) && (!is_numeric($body["FallbackID"]) || ($body["FallbackID"] < -1 || $body["FallbackID"] == 0))) {
            throw new InvalidParameterException("FallbackID must be -1 or greater than 0");
        }

        return $body;

    }


    /* End PutDepartment */
}
