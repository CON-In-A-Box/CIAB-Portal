<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Department;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\NotFoundException;
use App\Controller\IncludeResource;

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
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/department_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

class ListDeadlines extends \App\Controller\Deadline\BaseDeadline
{


    public function __construct($container)
    {
        parent::__construct($container);
        $this->includes = [
        new IncludeResource('\App\Controller\Department\GetDepartment', 'name', 'department')
        ];

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $department = $this->getDepartment($params['name']);
        $permissions = ['api.get.deadline.all',
        'api.get.deadline.'.$department['id']];
        $this->checkPermissions($permissions);

        $sth = $this->container->db->prepare(
            "SELECT * FROM `Deadlines` WHERE DepartmentID = '".$department['id']."' ORDER BY `Deadline` ASC"
        );
        $sth->execute();
        $todos = $sth->fetchAll();
        $output = array();
        $output['type'] = 'deadline_list';
        $data = array();
        foreach ($todos as $entry) {
            $deadline = new \App\Controller\Deadline\GetDeadline($this->container);
            $result = $deadline->buildDeadline($request, $response, $entry['DeadlineID'], $entry['DepartmentID'], $entry['Deadline'], $entry['Note']);
            $data[] = $deadline->arrayResponse($request, $response, $result);
        }
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        $output];

    }


    /* end ListDeadlines */
}
