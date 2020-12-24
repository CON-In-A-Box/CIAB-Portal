<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\registration\Controller\Ticket;

use Slim\Http\Request;
use Slim\Http\Response;

class PickupTicket extends BaseTicket
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $id = $params['id'];
        $aid = $this->getAccount($id, $request, $response, 'api.registration.ticket.pickup');
        if (is_array($aid)) {
            return $aid;
        }

        $sql = "UPDATE `Registrations` SET `BadgesPickedUp` = 1 WHERE `RegistrationID` = $id AND `VoidDate` IS NULL;";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        if ($sth->rowCount() == 0) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Conflict', 'Pickup Ticket report Failed.', 409)];
        }

        return [null];

    }


    /* end PickupTicket */
}
