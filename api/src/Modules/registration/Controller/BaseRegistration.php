<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\registration\Controller;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\BaseController;

abstract class BaseRegistration extends BaseController
{


    public function __construct(Container $container)
    {
        parent::__construct('registration', $container);

    }


    protected function checkPutPermission($request, $response)
    {
        if (!\ciab\RBAC::havePermission('api.set.registration.configuration')) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }
        return null;

    }


    /* End BaseRegistration */
}
