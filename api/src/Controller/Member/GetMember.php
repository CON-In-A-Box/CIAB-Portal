<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Member;

use Slim\Http\Request;
use Slim\Http\Response;

class GetMember extends BaseMember
{


    public function __invoke(Request $request, Response $response, $args)
    {
        $data = $this->findMember($request, $response, $args, 'name');
        if (gettype($data) === 'object') {
            return $data;
        } else {
            $this->buildMemberHateoas($request);
            $output = array(
                'id' => $data['Id'],
                'firstName' => $data['First Name'],
                'lastName' => $data['Last Name'],
                'email' => $data['Email']
            );
            return $this->jsonResponse($request, $response, $output);
        }

    }


    /* end GetMember */
}
