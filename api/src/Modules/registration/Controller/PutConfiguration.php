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
        $check = $this->checkPutPermission($request, $response);
        if ($check != null) {
            return $check;
        }
        $body = $request->getParsedBody();
        return $this->putConfiguration($request, $response, $params, 'Registration_Configuration', $body);

    }


    /* end PutConfiguration */
}
