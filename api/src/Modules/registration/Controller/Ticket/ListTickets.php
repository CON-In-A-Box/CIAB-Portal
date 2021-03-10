<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\registration\Controller\Ticket;

use Slim\Http\Request;
use Slim\Http\Response;

use App\Controller\PermissionDeniedException;
use App\Controller\NotFoundException;

class ListTickets extends BaseTicketInclude
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $data = $this->findMemberId($request, $response, $params, 'member');
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
            throw new NotFoundException('Ticket Not Found');
        }
        $tickets = [];
        foreach ($data as $index => $ticket) {
            $tickets[] = $this->buildTicket($data[$index], $ticket);
        }
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


    /* end ListTickets */
}
