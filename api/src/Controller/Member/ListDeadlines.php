<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Get(
 *      tags={"members"},
 *      path="/member/{id}/deadlines",
 *      summary="Lists deadlines for a given member",
 *      @OA\Parameter(
 *          description="The id or login of the member",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(
 *              oneOf = {
 *                  @OA\Schema(
 *                      description="Member login",
 *                      type="string"
 *                  ),
 *                  @OA\Schema(
 *                      description="Member id",
 *                      type="integer"
 *                  )
 *              }
 *          )
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
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
 *              ref="#/components/schemas/deadline_list"
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/member_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Controller\Member;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Container;
use Atlas\Query\Select;
use App\Controller\IncludeResource;

class ListDeadlines extends \App\Controller\Deadline\BaseDeadline
{


    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->includes = [
        new IncludeResource('\App\Controller\Department\GetDepartment', 'name', 'department')
        ];

    }


    public function buildResource(Request $request, Response $response, $args): array
    {
        $data = $this->findMemberId($request, $response, $args, 'id');
        $user = $data['id'];
        $select = Select::new($this->container->db);
        $select->columns(...\App\Controller\Deadline\BaseDeadline::selectMapping());
        $select->from('Deadlines');

        $sub1 = $select->subselect()->columns('DepartmentID')->from('ConComList')->whereEquals(['AccountID' => $user]);
        $sub2 = $select->subselect()->columns('DepartmentID')->from('Departments')->where('`ParentDepartmentID` IN ', $sub1);

        $select->where('DepartmentID IN ', $sub1);
        $select->orWhere('DepartmentID IN ', $sub1);
        $select->orderBy('`Deadline` ASC');
        $data = $select->fetchAll();
        $output = array();
        $output['type'] = 'deadline_list';
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        $output];

    }


    /* end ListDeadlines */
}
