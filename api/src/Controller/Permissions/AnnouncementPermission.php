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
        $announce = new \App\Controller\Announcement\GetAnnouncement($container);
        \ciab\RBAC::customizeRBAC(array($announce, 'customizeAnnouncementRBAC'));

    }


    /* end AnnouncementPermission */
}
