<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\registration\Controller\Ticket;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;

class PrintQueue extends BaseTicket
{

    protected static $columnsToAttributes = [
    '"print_job"' => 'type',
    'RegistrationID' => 'id'
    ];


    public function buildResource(Request $request, Response $response, $params): array
    {
        $select = Select::new($this->container->db);
        $select->columns(...PrintQueue::selectMapping())
            ->from('Registrations')
            ->where('`PrintRequested` IS NOT NULL');
        if (array_key_exists('event', $params)) {
            $event = $params['event'];
        } else {
            $event = 'current';
        }
        $event = $this->getEvent($event)['id'];
        $select->whereEquals(['EventID' => $event]);

        $data = $select->fetchAll();
        $tickets = [];
        $path = $request->getUri()->getBaseUrl();
        foreach ($data as $ticket) {
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
