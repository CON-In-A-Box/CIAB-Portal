<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Event;

use Slim\Http\Request;
use Slim\Http\Response;

class GetEvent extends BaseEvent
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $sth = $this->container->db->prepare("SELECT * FROM `Events` WHERE `EventID` = ".$params['id']);
        $sth->execute();
        $data = $sth->fetchAll();
        if (empty($data)) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Not Found', 'Event Not Found', 404)];
        }

        $events = [];
        foreach ($data as $item) {
            $events[] = $this->processEvent($item);
        }
        $this->id = $events[0]['id'];
        $this->buildEventHateoas($request);

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $events[0]
        ];

    }


    /* end GetEvent */
}
