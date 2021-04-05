<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Get(
 *      tags={"deadlines"},
 *      path="/deadline/{id}",
 *      summary="Gets a deadline",
 *      @OA\Parameter(
 *          description="Id of the deadline",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Deadline found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/deadline"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/deadline_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Controller\Deadline;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;
use App\Controller\NotFoundException;
use \App\Controller\IncludeResource;

class GetDeadline extends BaseDeadline
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
        $select = Select::new($this->container->db);
        $select->columns(...BaseDeadline::selectMapping());
        $select->from('Deadlines')->whereEquals(['DeadlineID' => $params['id']]);
        $deadline = $select->fetchOne();
        if (empty($deadline)) {
            throw new NotFoundException('Deadline Not Found');
        }
        $permissions = ['api.get.deadline.'.$deadline['department'],
        'api.get.deadline.all'];
        $this->checkPermissions($permissions);
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $deadline
        ];

    }


    /* end GetDeadline */
}
