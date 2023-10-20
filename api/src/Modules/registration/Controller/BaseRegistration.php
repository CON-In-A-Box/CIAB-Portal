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


    public static function install($container): void
    {

    }


    public static function permissions($database): ?array
    {
        return(['api.set.registration.configuration',
            'api.registration.ticket.checkin', 'api.registration.ticket.delete',
            'api.registration.ticket.email', 'api.registration.ticket.get',
            'api.registration.ticket.list', 'api.registration.ticket.lost',
            'api.registration.ticket.pickup', 'api.registration.ticket.post',
            'api.registration.ticket.print', 'api.registration.ticket.put',
            'api.registration.ticket.unvoid', 'api.registration.ticket.void' ]);

    }


    protected function checkPutPermission()
    {
        $permissions = ['api.set.registration.configuration'];
        $this->checkPermissions($permissions);

    }


    /* End BaseRegistration */
}
