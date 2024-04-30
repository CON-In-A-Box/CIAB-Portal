<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"members"},
 *      path="/member/{id}",
 *      summary="Gets a member",
 *      @OA\Parameter(
 *          description="Id of the member.",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="string")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Member found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/member"
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
 *      path="/member",
 *      summary="Gets current member",
 *      @OA\Response(
 *          response=200,
 *          description="Member found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/member"
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

namespace App\Controller\Member;

use Slim\Http\Request;
use Slim\Http\Response;

class GetMember extends BaseMember
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $id = null;
        if (array_key_exists('id', $params)) {
            $id = $params['id'];
        }
        if ($id == null) {
            $id = $request->getAttribute('oauth2-token')['user_id'];
        }
        $data = $this->getMember($request, $id, null, $this->internal)[0];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $data];

    }


    /* end GetMember */
}
