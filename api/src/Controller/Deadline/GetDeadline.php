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
 *          ref="#/components/responses/deadline_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Controller\Deadline;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;
use App\Error\NotFoundException;
use \App\Controller\IncludeResource;

class GetDeadline extends BaseDeadline
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
        $deadline = Select::new($this->container->db)
            ->columns(...BaseDeadline::selectMapping())
            ->from('Deadlines')
            ->whereEquals(['DeadlineID' => $params['id']])
            ->fetchOne();
        if (empty($deadline)) {
            throw new NotFoundException('Deadline Not Found');
        }

        $this->verifyScope($deadline);

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $deadline
        ];

    }


    /* end GetDeadline */
}
