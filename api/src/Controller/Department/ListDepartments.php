<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"departments"},
 *      path="/department",
 *      summary="Lists departments",
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/max_results",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/page_token",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="OK",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/department_list"
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Controller\Department;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

use App\Service\DepartmentService;

class ListDepartments extends BaseDepartment
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
        $output = $this->departmentService->listAll();
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $output,
        array('type' => 'department_list')];

    }


    /* end ListDepartments */
}
