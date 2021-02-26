<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Event;

use Slim\Http\Request;
use Slim\Http\Response;

class DeleteEvent extends BaseEvent
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.delete.event'];
        $this->checkPermissions($permissions);

        $sth = $this->container->db->prepare("DELETE FROM `Events` WHERE `EventID` = ".$params['id'].";");
        $sth->execute();
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        [null],
        204
        ];

    }


    /* end DeleteEvent */
}
