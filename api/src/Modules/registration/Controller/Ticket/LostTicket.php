<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\registration\Controller\Ticket;

use Slim\Http\Request;
use Slim\Http\Response;

class LostTicket extends BaseTicket
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $id = $params['id'];
        $sql = "UPDATE `Registrations` SET `BadgesPickedUp` = `BadgesPickedUp` + 1  WHERE `RegistrationID` = $id AND `VoidDate` IS NULL;";
        return $this->updateAndPrintTicket(
            $request,
            $response,
            $params,
            $id,
            'api.registration.ticket.lost',
            $sql,
            'Lost Ticket report Failed.'
        );

    }


    /* end LostTicket */
}
