<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\registration\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

class PutConfiguration extends BaseRegistration
{

    use \App\Controller\TraitConfiguration;


    public function buildResource(Request $request, Response $response, $params): array
    {
        $this->checkPutPermission();
        $body = $request->getParsedBody();
        $this->putConfiguration($request, $response, $params, 'Registration_Configuration', $body);

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
