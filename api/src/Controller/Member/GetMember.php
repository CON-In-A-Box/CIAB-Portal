<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Member;

use Slim\Http\Request;
use Slim\Http\Response;

class GetMember extends BaseMember
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $data = $this->findMemberId($request, $response, $args, 'id');
        $this->buildMemberHateoas($request);
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $data];

    }


    /* end GetMember */
}
