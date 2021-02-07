<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\registration\Controller\Ticket;

use Slim\Http\Request;
use Slim\Http\Response;

class UnvoidTicket extends BaseTicket
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $id = $params['id'];
        $sql = "UPDATE `Registrations` SET `VoidDate` = NULL, `VoidBy` = NULL, `VoidReason` = NULL WHERE RegistrationID = $id AND `VoidDate` IS NOT NULL";

        return $this->updateTicket(
            $request,
            $response,
            $params,
            'api.registration.ticket.unvoid',
            $sql,
            'Could not update.'
        );

    }


    public function processIncludes(Request $request, Response $response, $args, $values, &$data)
    {
        $this->ticketIncludes($request, $response, $args, $values, $data);

    }


    /* end UnvoidTicket */
}
