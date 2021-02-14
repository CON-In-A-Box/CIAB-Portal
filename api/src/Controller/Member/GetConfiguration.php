<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Member;

use Slim\Http\Request;
use Slim\Http\Response;

use App\Controller\PermissionDeniedException;

class GetConfiguration extends BaseMember
{


    use \App\Controller\TraitConfiguration;


    public function buildResource(Request $request, Response $response, $args): array
    {
        $data = $this->findMemberId($request, $response, $args, 'id');
        $user = $request->getAttribute('oauth2-token')['user_id'];
        if ($user != $data['id'] && !\ciab\RBAC::havePermission("api.get.configuration")) {
            throw new PermissionDeniedException();
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
