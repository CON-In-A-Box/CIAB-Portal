<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Member;

use Slim\Http\Request;
use Slim\Http\Response;

class GetConfiguration extends BaseMember
{


    use \App\Controller\TraitConfiguration;


    public function buildResource(Request $request, Response $response, $args): array
    {
        $data = $this->findMemberId($request, $response, $args, 'id');
        if (gettype($data) === 'object') {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $data];
        }
        $user = $request->getAttribute('oauth2-token')['user_id'];
        if ($user != $data['id'] && !\ciab\RBAC::havePermission("api.get.configuration")) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }

        $result = $this->getConfiguration($args, 'AccountConfiguration', "AND a.AccountId = {$data['id']}");

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
        $result[1]['account'] = $user;
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $result[0]
        ];

    }


    /* end GetConfiguration */
}
