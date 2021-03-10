<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Member;

use Slim\Http\Request;
use Slim\Http\Response;

class FindMembers extends BaseMember
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $query = $request->getQueryParam('q');

        $result = [];
        if ($query !== null) {
            $data = \lookup_users_by_key($query, false, true, false);
            foreach ($data['users'] as $user) {
                $link[] = [
                'method' => 'self',
                'href' => $request->getUri()->getBaseUrl().'/member/'.$user['Id'],
                'request' => 'GET'
                ];
                $result[] = ['id' => $user['Id'],
                'self' => $link];
            }
        }

        $output = ['type' => 'member_list'];
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $result,
        $output];

    }


    /* end FindMembers */
}
