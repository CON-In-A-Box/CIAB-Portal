<?php declare(strict_types=1);

/**
 * @OA\Get(
 *     tags={"departments"},
 *     path="/divisions",
 *     summary="Lists all divisions and their departments",
 *     @OA\Response(
 *         response=200,
 *         description="OK",
 *         @OA\JsonContent(
 *             ref="#/components/schemas/division_list"
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         ref="#/components/responses/401"
 *     ),
 *     security={{"ciab_auth":{}}}
 * )
 *
 * @OA\Schema(
 *    schema="division_list",
 *    allOf = {
 *        @OA\Schema(ref="#/components/schemas/resource_list")
 *    },
 *    @OA\Property(
 *        property="type",
 *        type="string",
 *        enum={"division_list"}
 *    ),
 *    @OA\Property(
 *        property="data",
 *        type="array",
 *        description="List of divisions and departments",
 *        @OA\Items(
 *            ref="#/components/schemas/department"
 *        )
 *    )
 * )
 */
namespace App\Controller\Department;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class ListDivisions
{

    protected $departmentService;


    public function __construct(Container $container)
    {
        $this->departmentService = $container->get('department_service');

    }


    public function __invoke(Request $request, Response $response, $args)
    {
        $divisions = $this->departmentService->getStaffDivisions();
        return $response->withJson($divisions, 200);

    }


    /* End ListDivisions */
}
