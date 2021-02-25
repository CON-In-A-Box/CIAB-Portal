<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\registration\Controller\Ticket;

use Slim\Http\Request;
use Slim\Http\Response;

use App\Controller\InvalidParameterException;
use App\Controller\ConflictException;

class PostTicket extends BaseTicket
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
        if (array_key_exists('ticketType', $body)) {
            $type = $body['ticketType'];
        } else {
            throw new InvalidParameterException('Required \'ticketType\' parameter not present');
        }

        if (array_key_exists('event', $body)) {
            $event = $body['event'];
        } else {
            $event = \current_eventID();
        }

        if (array_key_exists('dependOn', $body)) {
            $depend = $body['dependOn'];
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
            $regBy = $body['registeredBy'];
        } else {
            $regBy = $body['member'];
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


    public function processIncludes(Request $request, Response $response, $args, $values, &$data)
    {
        $this->ticketIncludes($request, $response, $args, $values, $data);

    }


    /* end PostTicket */
}
