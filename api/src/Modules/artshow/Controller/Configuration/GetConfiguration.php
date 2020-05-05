<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Configuration;

use Slim\Http\Request;
use Slim\Http\Response;

class GetConfiguration extends BaseConfiguration
{

    use \App\Controller\TraitConfiguration;


    public function buildResource(Request $request, Response $response, $params): array
    {
        if (!\ciab\RBAC::havePermission("api.get.configuration")) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }

        $result = $this->getConfiguration($params, 'Artshow_Configuration');

        if (count($result) > 1) {
            $output = [];
            $output['type'] = 'configuration_list';
            return [
            \App\Controller\BaseController::LIST_TYPE,
            $result,
            $output
            ];
        }
        $result[0]['type'] = 'configuration';
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $result[0]
        ];

    }


    /* end GetConfiguration */
}
