<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\registration\Controller\Ticket;

use Slim\Http\Request;
use Slim\Http\Response;

class CheckinTicket extends BaseTicket
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $id = $params['id'];
        $sql = "UPDATE `Registrations` SET `BoardingPassGenerated` = NOW() WHERE `RegistrationID` = $id AND `BoardingPassGenerated` IS NULL AND `VoidDate` IS NULL";
        return $this->updateAndPrintTicket(
            $request,
            $response,
            $params,
            $id,
            'api.registration.ticket.checkin',
            $sql,
            'Generation of Boarding Pass Failed.'
        );

    }


    /* end CheckinTicket */
}
