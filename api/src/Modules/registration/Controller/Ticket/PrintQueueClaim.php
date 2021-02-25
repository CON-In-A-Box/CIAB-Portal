<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\registration\Controller\Ticket;

use Slim\Http\Request;
use Slim\Http\Response;

class PrintQueueClaim extends BaseTicket
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $id = $params['id'];
        $sql = "UPDATE `Registrations` SET `PrintRequested` = NULL, `LastPrintedDate` = NOW() WHERE `RegistrationID` = $id AND `PrintRequested` IS NOT NULL";

        return $this->updateTicket(
            $request,
            $response,
            $params,
            'api.registration.ticket.print',
            $sql,
            'Not in Print Queue.'
        );

    }


    public function processIncludes(Request $request, Response $response, $args, $values, &$data)
    {
        $this->ticketIncludes($request, $response, $args, $values, $data);

    }


    /* end PrintQueueClaim */
}
