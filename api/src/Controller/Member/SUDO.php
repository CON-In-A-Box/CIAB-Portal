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
        $permissions = ['admin.sudo'];
        $this->checkPermissions($permissions);
        $token = $request->getAttribute('oauth2-token');
        $storage->setAccessToken($token['access_token'], $token['client_id'], $data['id'], $token['expires']);
        return [null];

    }


    /* end SUDO*/
}
