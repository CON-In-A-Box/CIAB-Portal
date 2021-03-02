<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\registration\Controller\Ticket;

use Slim\Http\Request;
use Slim\Http\Response;

use App\Controller\InvalidParameterException;
use App\Controller\ConflictException;

class PostTicket extends BaseTicketInclude
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.registration.ticket.post'];
        $this->checkPermissions($permissions);

        $body = $request->getParsedBody();
        if ($body && array_key_exists('member', $body)) {
            $member = $body['member'];
        } else {
            throw new InvalidParameterException('Required \'member\' parameter not present');
        }
        $data = $this->findMember($request, $response, $body, 'member');
        $member = $data['id'];

        if (array_key_exists('event', $body)) {
            $event = $body['event'];
        } else {
            $event = \current_eventID();
        }
        $target = new \App\Controller\Event\GetEvent($this->container);
        $target->buildResource($request, $response, ['id' => $event])[1];


        if (array_key_exists('ticketType', $body)) {
            $type = $body['ticketType'];
        } else {
            throw new InvalidParameterException('Required \'ticketType\' parameter not present');
        }
        $target = new GetTicketTypes($this->container);
        $target->buildResource($request, $response, ['id' => $type])[1];

        if (array_key_exists('dependOn', $body)) {
            $data = $this->findMember($request, $response, $body, 'dependOn');
            $depend = $data['id'];
        } else {
            $depend = 'NULL';
        }

        if (array_key_exists('badgeName', $body)) {
            $badgeName = \MyPDO::quote($body['badgeName']);
        } else {
            $badgeName = 'NULL';
        }

        if (array_key_exists('contact', $body)) {
            $contact = \MyPDO::quote($body['contact']);
        } else {
            $contact = 'NULL';
        }

        if (array_key_exists('registeredBy', $body)) {
            $data = $this->findMember($request, $response, $body, 'registeredBy');
            $regBy = $data['id'];
        } else {
            $regBy = $member;
        }

        $sql = "INSERT INTO Registrations(AccountId, BadgeDependentOnID, BadgeName, BadgeTypeID, EmergencyContact, EventID, RegisteredByID, RegistrationDate) VALUES ($member, $depend, $badgeName, $type, $contact, $event, $regBy, NOW())";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        if ($sth->rowCount() == 0) {
            throw new ConflictException('Could not update');
        }

        $target = new GetTicket($this->container);
        $data = $target->buildResource($request, $response, ['id' => $this->container->db->lastInsertId()])[1];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data),
        201
        ];

    }


    /* end PostTicket */
}
