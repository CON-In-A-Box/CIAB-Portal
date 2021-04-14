<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Post(
 *      tags={"members"},
 *      path="member/{id}/staff_membership",
 *      summary="Adds a new staff membership to a member",
 *      @OA\Parameter(
 *          description="The id or email of the member",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(
 *              oneOf = {
 *                  @OA\Schema(
 *                      description="Member ID",
 *                      type="integer"
 *                  ),
 *                  @OA\Schema(
 *                      description="Member login email",
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
 *                      property="Department",
 *                      description="Id or name of the department",
 *                      nullable=false,
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="Position",
 *                      description="ID of the position",
 *                      nullable=false,
 *                      type="integer"
 *                  ),
 *                  @OA\Property(
 *                      property="Note",
 *                      nullable=true,
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="Event",
 *                      description="If not present, current event",
 *                      nullable=true,
 *                      type="integer"
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


namespace App\Modules\staff\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\InvalidParameterException;
use Atlas\Query\Insert;

class PostStaffMembership extends BaseStaff
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        if (array_key_exists('id', $params)) {
            $user = $this->getMember($request, $params['id'])[0]['id'];
        } else {
            $user = $request->getAttribute('oauth2-token')['user_id'];
        }

        $body = $request->getParsedBody();
        if (!$body) {
            throw new InvalidParameterException("Body required");
        }

        if (!array_key_exists('Department', $body)) {
            throw new InvalidParameterException('Required \'Department\' parameter not present');
        }

        $department = $this->getDepartment($body['Department']);
        $permissions = ['api.post.staff.'.$department['id'],
        'api.post.staff.all'];
        $this->checkPermissions($permissions);

        $insert = Insert::new($this->container->db);
        $insert->into('ConComList');
        $insert->column('AccountID', $user);
        $insert->column('DepartmentID', $department['id']);

        if (!array_key_exists('Position', $body)) {
            throw new InvalidParameterException('Required \'Position\' parameter not present');
        }
        $insert->column('PositionID', $body['Position']);

        if (array_key_exists('Note', $body)) {
            $note = "'".$body['Note']."'";
            $insert->column('Note', $note);
        }

        if (array_key_exists('Event', $body)) {
            $event = $body['Event'];
        } else {
            $event = 'current';
        }
        $event = $this->getEvent($event)['id'];
        $insert->column('EventID', $event);
        $insert->perform();

        $target = new GetStaffMembership($this->container);
        $data = $target->buildResource($request, $response, ['id' => $insert->getLastInsertId()])[1];

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data),
        201
        ];

    }


    /* end PostStaffMembership */
}
