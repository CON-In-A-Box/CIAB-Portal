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


    protected function checkPutPermission()
    {
        $permissions = ['api.set.registration.configuration'];
        $this->checkPermissions($permissions);

    }


    /* End BaseRegistration */
}
