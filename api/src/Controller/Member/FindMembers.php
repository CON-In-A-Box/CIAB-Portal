<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"members"},
 *      path="/member/find",
 *      summary="Search for a member based on the query",
 *      @OA\Parameter(
 *          description="Query string",
 *          in="query",
 *          name="q",
 *          required=true,
 *          @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/max_results",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/page_token",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Member(s) found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/member_list"
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

class FindMembers extends BaseMember
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $query = $request->getQueryParam('q');

        $result = [];
        if ($query !== null) {
            $data = \lookup_users_by_key($query, false, true, false);
            foreach ($data['users'] as $user) {
                $link[] = [
                'method' => 'self',
                'href' => $request->getUri()->getBaseUrl().'/member/'.$user['Id'],
                'request' => 'GET'
                ];
                $result[] = ['id' => $user['Id'],
                'self' => $link];
            }
        }

        $output = ['type' => 'member_list'];
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $result,
        $output];

    }


    /* end FindMembers */
}
