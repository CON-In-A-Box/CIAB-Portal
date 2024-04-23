<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"registration"},
 *      path="/registration/ticket/printqueue",
 *      summary="Get the current print queue for badges for the event.",
 *      deprecated=true,
 *      @OA\Parameter(
 *          ref="#/components/parameters/event",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/max_results",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/page_token",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="OK",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/print_queue"
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

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
        $event = $this->getEventId($request);
        $select = Select::new($this->container->db);
        $select->columns(...PrintQueue::selectMapping())
            ->from('Registrations')
            ->where('`PrintRequested` IS NOT NULL')
            ->whereEquals(['EventID' => $event]);

        $data = $select->fetchAll();
        $tickets = [];
        $path = $request->getUri()->getBaseUrl();
        foreach ($data as $ticket) {
            $ticket['claim'] = [
            'type' => 'print_job',
            'method' => 'claim',
            'id' => $ticket['id'],
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
