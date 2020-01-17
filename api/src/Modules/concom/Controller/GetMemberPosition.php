<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\concom\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

require_once __DIR__.'/../../../../../modules/concom/functions/POSITION.inc';

class GetMemberPosition extends BaseConcom
{


    public function __invoke(Request $request, Response $response, $args)
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
                return $this->errorResponse($request, $response, $error, 'Not Found', 404);
            }
            if ($data[0]['Id'] != $user &&
                !\ciab\RBAC::havePermission('api.get.concom')) {
                return $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403);
            }
            $user = $data['users'][0]['Id'];
        }
        $concom = \concom\POSITION::getConComPosition($user);
        $data = [];
        $path = $request->getUri()->getBaseUrl();
        foreach ($concom as $entry) {
            $data[] = $this->buildEntry($request, $entry['departmentId'], $user, $entry['note'], $entry['position']);
        }
        return $this->listResponse(
            $request,
            $response,
            array('type' => 'concom_list'),
            $data
        );

    }


    /* end GetMemberPosition */
}
