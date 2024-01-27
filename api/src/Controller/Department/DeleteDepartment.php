<?php declare(strict_types=1);

/**
 * @OA\Delete(
 *     tags={"departments"},
 *     path="/department/{id}",
 *     summary="Delete an existing department",
 *     @OA\Parameter(
 *         description="ID of the department being deleted",
 *         in="path",
 *         name="id",
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

use App\Service\DepartmentService;

class DeleteDepartment extends BaseDepartment
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

        $this->departmentService->deleteById($departmentId);

        return [
            \App\Controller\BaseController::RESULT_TYPE,
            [null],
            204
        ];

    }


    /* End DeleteDepartment */
}
