<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Get(
 *      tags={"departments"},
 *      path="/department/{id}/deadlines",
 *      summary="Lists deadlines for a given department",
 *      @OA\Parameter(
 *          description="The id or name of the department",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(
 *              oneOf = {
 *                  @OA\Schema(
 *                      description="Department id",
 *                      type="integer"
 *                  ),
 *                  @OA\Schema(
 *                      description="Department name",
 *                      type="string"
 *                  )
 *              }
 *          )
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="OK",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/deadline_list"
 *          )
 *      ),
 *      @OA\Response(
 *          response=400,
 *          ref="#/components/responses/400"
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=403,
 *          ref="#/components/responses/403"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/department_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Controller\Deadline;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;
use App\Error\NotFoundException;
use App\Controller\IncludeResource;

class ListDepartmentDeadlines extends BaseDeadline
{


    public function __construct($container)
    {
        parent::__construct($container);
        $this->includes = [
        new IncludeResource('\App\Controller\Department\GetDepartment', 'name', 'department'),
        new IncludeResource('\App\Controller\Member\GetMember', 'id', 'posted_by')
        ];

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $department = $this->getDepartment($params['name']);
        $data = Select::new($this->container->db)
            ->columns(...BaseDeadline::selectMapping())
            ->from('Deadlines')->whereEquals(['DepartmentID' => $department['id']])
            ->orderBy('`Deadline` ASC')
            ->fetchAll();

        $data = $this->filterScope($data);

        $output = array();
        $output['type'] = 'deadline_list';
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        $output];

    }


    /* end ListDepartmentDeadlines */
}
