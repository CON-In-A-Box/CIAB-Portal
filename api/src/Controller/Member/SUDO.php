<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Post(
 *      tags={"administrative"},
 *      path="/admin/SUDO/{id}",
 *      summary="Convert current session to that of another member.",
 *      @OA\Parameter(
 *          description="The id or login of the member",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(
 *              description="Member login or id",
 *              type="string"
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="OK"
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

//require_once __DIR__.'/../../../../backends/oauth2.inc';

class SUDO extends BaseMember
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        global $storage;

        $id = $this->getMember($request, $params['name'])[0]['id'];
        $permissions = ['admin.sudo'];
        $this->checkPermissions($permissions);
        $token = $request->getAttribute('oauth2-token');
        $storage->setAccessToken($token['access_token'], $token['client_id'], $id, $token['expires']);
        return [null];

    }


    /* end SUDO*/
}
