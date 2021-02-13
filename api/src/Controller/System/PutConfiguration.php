<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\System;

use Slim\Http\Request;
use Slim\Http\Response;

class PutConfiguration extends BaseSystem
{

    use \App\Controller\TraitConfiguration;


    public function buildResource(Request $request, Response $response, $args): array
    {
        $permissions = ['api.put.configuration'];
        $this->checkPermissions($permissions);
        $body = $request->getParsedBody();
        return $this->putConfiguration($request, $response, $args, 'Configuration', $body);

    }


    /* end PutConfiguration */
}
