<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\registration\Controller\Ticket;

use Slim\Http\Request;
use Slim\Http\Response;

class GetTicket extends BaseTicket
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $id = $params['id'];
        $aid = $this->getAccount($id, $request, $response, 'api.registration.ticket.get');
        if (is_array($aid)) {
            return $aid;
        }
        $sql = "SELECT * FROM `Registrations` WHERE  RegistrationID = $id";

        $query = $request->getQueryParams();
        if (!array_key_exists('showVoid', $query) || !boolval($query['showVoid'])) {
            $sql .= " AND `VoidDate` IS NULL";
        }

        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll();
        if (empty($data)) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Not Found', 'Ticket Not Found', 404)];
        }

        $ticket = $this->buildTicket($data[0], $data[0]);
        $this->buildTicketHateoas($request, $ticket['id']);

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $ticket
        ];

    }


    public function processIncludes(Request $request, Response $response, $args, $values, &$data)
    {
        $this->ticketIncludes($request, $response, $args, $values, $data);

    }


    /* end GetTicket */
}
