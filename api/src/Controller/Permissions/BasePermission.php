<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Permissions;

use Slim\Container;
use App\Controller\BaseController;

abstract class BasePermission extends BaseController
{


    public function __construct(Container $container)
    {
        parent::__construct('permission', $container);

    }


    protected function buildBaseEntry($allowed, $subtype, $method, $hateoas)
    {
        return [
        'type' => 'permission_entry',
        'subtype' => $subtype.'_'.$method,
        'allowed' => $allowed,
        'action' => $hateoas
        ];

    }


    /* End BasePermission */
}
