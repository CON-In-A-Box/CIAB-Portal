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
        $data = $this->findMember($request, $response, $args, 'name');
        if (gettype($data) === 'object') {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $data];
        } else {
            $this->buildMemberHateoas($request);
            $output = array(
                'id' => $data['Id'],
                'firstName' => $data['First Name'],
                'lastName' => $data['Last Name'],
                'email' => $data['Email']
            );
            return [
            \App\Controller\BaseController::RESOURCE_TYPE,
            $output];
        }

    }


    /* end GetMember */
}
