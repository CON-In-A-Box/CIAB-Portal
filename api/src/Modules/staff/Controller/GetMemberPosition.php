<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Get(
 *      tags={"members"},
 *      path="/member/{id}/staff_membership",
 *      summary="Gets staff positions for a member",
 *      @OA\Parameter(
 *          description="Id of the member.",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/event",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/max_results",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/page_token",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Member staff positions found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/staff_list"
 *          ),
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
 *
 *  @OA\Get(
 *      tags={"members"},
 *      path="/member/staff_membership/",
 *      summary="Gets staff positions for the current member",
 *      @OA\Parameter(
 *          ref="#/components/parameters/max_results",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/page_token",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Member staff positions found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/staff_list"
 *          ),
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

namespace App\Modules\staff\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\PermissionDeniedException;

class GetMemberPosition extends BaseStaff
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $user = $request->getAttribute('oauth2-token')['user_id'];
        if (array_key_exists('id', $params)) {
            $id = $this->getMember($request, $params['id'])[0]['id'];
            if ($id != $user &&
                !\ciab\RBAC::havePermission('api.get.staff')) {
                throw new PermissionDeniedException();
            }
            $user = $id;
        }
        $event = $this->getEventId($request);
        $data = $this->selectStaff($event, null, $user);
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        array('type' => 'staff_list')
        ];

    }


    /* end GetMemberPosition */
}
