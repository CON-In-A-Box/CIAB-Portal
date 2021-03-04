<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\registration\Controller\Ticket;

use Slim\Http\Request;
use Slim\Http\Response;

use App\Controller\ConflictException;
use App\Controller\InvalidParameterException;

class PutTicket extends BaseTicketInclude
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $id = $params['id'];
        $aid = $this->getAccount($id, $request, $response, 'api.registration.ticket.put');

        $body = $request->getParsedBody();
        if (empty($body)) {
            throw new InvalidParameterException('Body not present');
        }
        $set = [];

        if (array_key_exists('badgeName', $body)) {
            $set[] = '`BadgeName` = '.\MyPDO::quote($body['badgeName']);
        }
        if (array_key_exists('contact', $body)) {
            $set[] = '`EmergencyContact` = '.\MyPDO::quote($body['contact']);
        }

        if (array_key_exists('note', $body)) {
            $set[] = '`Note` = '.\MyPDO::quote($body['note']);
        }

        if (empty($set)) {
            throw new InvalidParameterException('Understood Parameter missing.');
        }

        $setStr = implode(', ', $set);

        $sql = "UPDATE `Registrations` SET $setStr WHERE RegistrationID = $id";

        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        if ($sth->rowCount() == 0) {
            throw new ConflictException('Could not update');
        }

        $target = new GetTicket($this->container);
        $newdata = $target->buildResource($request, $response, $params)[1];
        $data = $target->arrayResponse($request, $response, $newdata);

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $data
        ];

    }


    /* end PutTicket */
}
