<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Permissions;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class AnnouncementPermission extends BasePermission
{


    public function __construct(Container $container)
    {
        parent::__construct($container, 'announcement', ['put', 'post', 'delete']);

    }


    /* end AnnouncementPermission */
}
