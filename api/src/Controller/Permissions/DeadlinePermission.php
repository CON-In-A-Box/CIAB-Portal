<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Permissions;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;

abstract class DeadlinePermission extends BasePermission
{


    public function __construct(Container $container)
    {
        parent::__construct($container, 'deadline', ['get', 'put', 'post', 'delete']);
        \ciab\RBAC::customizeRBAC('\App\Controller\Deadline\BaseDeadline::customizeDeadlineRBAC');

    }


    /* end DeadlinePermission */
}
