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
 *          description="Include the resource instead of the ID.",
 *          in="query",
 *          name="include",
 *          required=false,
 *          explode=false,
 *          style="form",
 *          @OA\Schema(
 *              type="array",
 *              @OA\Items(
 *                  type="string",
 *                  enum={"id", "fallback","division"}
 *              )
 *           )
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/maxResults",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/pageToken",
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

require_once __DIR__.'/../../../../functions/divisional.inc';

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\IncludeResource;

class ListDepartments extends BaseDepartment
{


    public function __construct($container)
    {
        parent::__construct($container);
        $this->includes = [
        new IncludeResource('\App\Controller\Department\GetDepartment', 'name', 'id')
        ];

    }


    public function buildResource(Request $request, Response $response, $args): array
    {
        global $Departments;
        $output = array();
        foreach ($Departments as $key => $data) {
            $output[] = [
            'type' => 'department_entry',
            'id' => $data['id'],
            'get' => $this->buildDepartmentGet($request, $data['id'])
            ];
        }
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $output,
        array('type' => 'department_list')];

    }


    /* end ListDepartments */
}
