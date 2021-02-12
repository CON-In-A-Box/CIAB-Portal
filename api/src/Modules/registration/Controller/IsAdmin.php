<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\registration\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

class IsAdmin extends BaseRegistration
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $this->checkPutPermission();
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        ['admin' => true]];

    }


    /* end IsAdmin */
}
