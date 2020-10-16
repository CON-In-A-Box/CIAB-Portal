<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Member;

use Slim\Http\Request;
use Slim\Http\Response;

class PostPassword extends BaseMember
{

    public $privilaged = false;


    public function buildResource(Request $request, Response $response, $args): array
    {
        $data = $this->findMember($request, $response, $args, 'name');
        if (gettype($data) === 'object') {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $data];
        }
        $password = \rppg();
        \reset_password($data['Email'], $password);
        return [null];

    }


    /* end PostPassword */
}
