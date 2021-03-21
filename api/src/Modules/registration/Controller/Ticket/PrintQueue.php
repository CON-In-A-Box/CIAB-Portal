<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\registration\Controller\Ticket;

use Slim\Http\Request;
use Slim\Http\Response;

class PrintQueue extends BaseTicket
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $sql = "SELECT `RegistrationID` FROM `Registrations` WHERE `PrintRequested` IS NOT NULL";
        if (array_key_exists('event', $params)) {
            $event = $params['event'];
        } else {
            $event = 'current';
        }
        $event = $this->getEvent($event)['id'];
        $sql .= ' AND `EventID` = '.$event;

        $target = new \App\Controller\Event\GetEvent($this->container);
        $target->buildResource($request, $response, ['id' => $event])[1];

        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll();
        $tickets = [];
        $path = $request->getUri()->getBaseUrl();
        foreach ($data as $index => $ticket) {
            $ticket['type'] = 'print_job';
            $ticket['id'] = $ticket['RegistrationID'];
            unset($ticket['RegistrationID']);
            $ticket['claim'] = [
            'method' => 'claim',
            'href' => $path.'/registration/ticket/printqueue/claim/'.$ticket['id'],
            'request' => 'PUT'
            ];
            $tickets[] = $ticket;
        }
        $output = ['type' => 'print_queue'];
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $tickets,
        $output
        ];

    }


    /* end PrintQueue */
}
