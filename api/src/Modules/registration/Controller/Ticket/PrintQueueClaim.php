<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\registration\Controller\Ticket;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Update;

class PrintQueueClaim extends BaseTicketInclude
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $update = Update::new($this->container->db)
            ->table('Registrations')
            ->columns(['PrintRequested' => null])
            ->set('LastPrintedDate', 'NOW()')
            ->whereEquals(['RegistrationID' => $params['id']])
            ->where('`PrintRequested` IS NOT NULL ');

        return $this->updateTicket(
            $request,
            $response,
            $params,
            'api.registration.ticket.print',
            $update,
            'Not in Print Queue.'
        );

    }


    /* end PrintQueueClaim */
}
