<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\System;

use Slim\Http\Request;
use Slim\Http\Response;

use App\Controller\InvalidParameterException;

class PutConfiguration extends BaseSystem
{

    use \App\Controller\TraitConfiguration;


    public function buildResource(Request $request, Response $response, $args): array
    {
        $permissions = ['api.put.configuration'];
        $this->checkPermissions($permissions);
        $body = $request->getParsedBody();
        if (empty($body)) {
            throw new InvalidParameterException('No update parameter present');
        }
        $this->putConfiguration($request, $response, $args, 'Configuration', $body);

        $target = new GetConfiguration($this->container);
        $args['key'] = $body['Field'];
        $data = $target->buildResource($request, $response, $args)[1];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data)
        ];

    }


    /* end PutConfiguration */
}
