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
 *                  enum={"departmentId"}
 *              )
 *           )
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
use App\Controller\NotFoundException;
use \App\Controller\IncludeResource;

class GetDeadline extends BaseDeadline
{


    public function __construct($container)
    {
        parent::__construct($container);
        $this->includes = [
        new IncludeResource('\App\Controller\Department\GetDepartment', 'name', 'departmentId')
        ];

    }


    public function buildResource(Request $request, Response $response, $args): array
    {
        $sth = $this->container->db->prepare("SELECT * FROM `Deadlines` WHERE `DeadlineID` = '".$args['id']."'");
        $sth->execute();
        $deadlines = $sth->fetchAll();
        if (empty($deadlines)) {
            throw new NotFoundException('Deadline Not Found');
        }
        $permissions = ['api.get.deadline.'.$deadlines[0]['DepartmentID'],
        'api.get.deadline.all'];
        $this->checkPermissions($permissions);
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $this->buildDeadline(
            $request,
            $response,
            $deadlines[0]['DeadlineID'],
            $deadlines[0]['DepartmentID'],
            $deadlines[0]['Deadline'],
            $deadlines[0]['Note']
        )];

    }


    /* end GetDeadline */
}
