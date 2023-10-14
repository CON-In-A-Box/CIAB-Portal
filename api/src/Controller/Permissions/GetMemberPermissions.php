<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"members"},
 *      path="/member/{id}/permissions",
 *      summary="Gets a member permissions",
 *      @OA\Parameter(
 *          description="Id of the member.",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Member found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/permission_list"
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

namespace App\Controller\Permissions;

use App\Controller\BaseController;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class GetMemberPermissions extends BaseController
{


    public function __construct(Container $container)
    {
        parent::__construct('permission', $container);

    }


    public static function install($database): void
    {

    }


    public static function permissions($database): ?array
    {
        return null;

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $id = null;
        if (array_key_exists('id', $params)) {
            $id = $params['id'];
        }
        if ($id == null) {
            $id = $request->getAttribute('oauth2-token')['user_id'];
        }

        $data = \ciab\RBAC::getMemberPermissions($id);
        $result = array();
        foreach ($data as $entry) {
            $result[] = [
                'type' => 'permission_entry',
                'subtype' => $entry,
                'allowed' => true,
                ];
        }

        return [
        \App\Controller\BaseController::LIST_TYPE,
        $result,
        array('type' => 'permission_list')];

    }


    /* end GetMemberPermissions */
}
