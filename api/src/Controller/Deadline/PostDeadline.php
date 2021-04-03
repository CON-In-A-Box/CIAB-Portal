<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Post(
 *      tags={"departments"},
 *      path="/department/{id}/deadline",
 *      summary="Adds a new deadline",
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
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="deadline",
 *                      type="string",
 *                      format="date"
 *                  ),
 *                  @OA\Property(
 *                      property="note",
 *                      type="string"
 *                  )
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=201,
 *          description="OK"
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

namespace App\Controller\Deadline;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Insert;
use App\Controller\InvalidParameterException;

class PostDeadline extends BaseDeadline
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $department = $this->getDepartment($params['name']);
        $permissions = ['api.post.deadline.'.$department['id'],
        'api.post.deadline.all'];
        $this->checkPermissions($permissions);

        $required = ['deadline', 'note'];
        $body = $this->checkRequiredBody($request, $required);

        $date = strtotime($body['deadline']);
        if ($date == false) {
            throw new InvalidParameterException('\'deadline\' parameter not valid \''.$body['deadline'].'\'');
        }
        if ($date < strtotime('now')) {
            throw new InvalidParameterException('\'deadline\' parameter in the past not valid \''.$body['deadline'].'\'');
        }
        $body['deadline'] = date("Y-m-d", $date);
        $body['department'] = $department['id'];

        $insert = Insert::new($this->container->db);
        $insert->into('Deadlines');
        $insert->columns(BaseDeadline::insertPayloadFromParams($body));
        $insert->perform();
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        [null],
        201
        ];

    }


    /* end PostDeadline */
}
