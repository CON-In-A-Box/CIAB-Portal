<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Permissions;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class GenericPermission extends BasePermission
{

    const ALL_METHODS = ['get', 'put', 'post', 'delete'];


    public function __construct(Container $container)
    {
        parent::__construct($container, 'generic');

    }


    /* end GenericPermission */
}
