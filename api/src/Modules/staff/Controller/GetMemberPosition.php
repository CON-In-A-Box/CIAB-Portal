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
 *          ref="#/components/parameters/maxResults",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/pageToken",
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
 *          ref="#/components/parameters/maxResults",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/pageToken",
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
use App\Controller\NotFoundException;

class GetMemberPosition extends BaseStaff
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $user = $request->getAttribute('oauth2-token')['user_id'];
        if (array_key_exists('id', $args)) {
            $data = \lookup_users_by_key($args['id']);
            if (empty($data['users'])) {
                if (empty($data['error'])) {
                    $error = 'No Members Found';
                } else {
                    $error = $data['error'];
                }
                throw new NotFoundException($error);
            }
            if ($data['users'][0]['Id'] != $user &&
                !\ciab\RBAC::havePermission('api.get.staff')) {
                throw new PermissionDeniedException();
            }
            $user = $data['users'][0]['Id'];
        }
        $staff = $this->getStaffPosition($user, 'current');
        $data = [];
        $path = $request->getUri()->getBaseUrl();
        foreach ($staff as $entry) {
            $data[] = $this->buildEntry($request, $entry['ListRecordID'], $entry['DepartmentID'], $user, $entry['Note'], $entry['Position']);
        }
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        array('type' => 'staff_list')
        ];

    }


    /* end GetMemberPosition */
}
