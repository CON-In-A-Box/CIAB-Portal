<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\registration\Controller\Ticket;

use Slim\Http\Request;
use Slim\Http\Response;

use App\Controller\PermissionDeniedException;

class ListTickets extends BaseTicket
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $data = $this->findMemberId($request, $response, $params, 'member');
        if (gettype($data) === 'object') {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $data];
        }
        $aid = $data['id'];

        $user = $request->getAttribute('oauth2-token')['user_id'];
        if ($user != $aid &&
            !\ciab\RBAC::havePermission('api.registration.ticket.list')) {
            throw new PermissionDeniedException();
        }

        $sql = "SELECT * FROM `Registrations` WHERE ( `RegisteredByID` = $aid OR `AccountID` = $aid)";
        if (array_key_exists('event', $params)) {
            $sql .= ' AND `EventID` = '.$params['event'];
        }
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
        $tickets = [];
        foreach ($data as $index => $ticket) {
            $tickets[] = $this->buildTicket($data[$index], $ticket);
        }
        #$this->buildTicketHateoas($request);
        if (count($tickets) > 1) {
            $output = ['type' => 'ticket_list'];
            return [
            \App\Controller\BaseController::LIST_TYPE,
            $tickets,
            $output
            ];
        } else {
            return [
            \App\Controller\BaseController::RESOURCE_TYPE,
            $tickets[0]
            ];
        }

    }


    public function processIncludes(Request $request, Response $response, $args, $values, &$data)
    {
        $this->ticketIncludes($request, $response, $args, $values, $data);

    }


    /* end ListTickets */
}
