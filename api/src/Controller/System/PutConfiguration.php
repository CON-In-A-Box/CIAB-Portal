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
        if (!\ciab\RBAC::havePermission("api.put.configuration")) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }

        $body = $request->getParsedBody();
        return $this->putConfiguration($request, $response, $args, 'Configuration', $body);

    }


    /* end PutConfiguration */
}
