<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Member;

use Slim\Http\Request;
use Slim\Http\Response;

//require_once __DIR__.'/../../../../backends/oauth2.inc';

class SUDO extends BaseMember
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        global $storage;

        $data = $this->findMember($request, $response, $args, 'name');
        if (gettype($data) === 'object') {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $data];
        } else {
            if (\ciab\RBAC::havePermission('admin.sudo')) {
                $token = $request->getAttribute('oauth2-token');
                $storage->setAccessToken($token['access_token'], $token['client_id'], $data['Id'], $token['expires']);
                return [null];
            } else {
                return [
                \App\Controller\BaseController::RESULT_TYPE,
                $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
            }
        }

    }


    /* end SUDO*/
}
