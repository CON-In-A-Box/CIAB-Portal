<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Member;

use Slim\Http\Request;
use Slim\Http\Response;

class GetMember extends BaseMember
{


    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $request->getAttribute('oauth2-token')['user_id'];
        if (array_key_exists('name', $args)) {
            $data = \lookup_users_by_key($args['name']);
            if (empty($data['users'])) {
                if (empty($data['error'])) {
                    $error = 'No Members Found';
                } else {
                    $error = $data['error'];
                }
                return $this->errorResponse($request, $response, $error, 'Not Found', 404);
            }
            $data = $data['users'][0];
            if ($data[0]['Id'] != $user &&
                !\ciab\RBAC::havePermission('api.get.member')) {
                return $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403);
            }
        } else {
            $data = \lookup_user_by_id($user);
            $data = $data['users'][0];
        }
        return $this->jsonResponse($request, $response, $data);

    }


    /* end GetMember */
}
