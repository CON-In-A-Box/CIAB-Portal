<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\registration\Controller\Ticket;

use Slim\Http\Request;
use Slim\Http\Response;

class PutTicket extends BaseTicket
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $id = $params['id'];
        $aid = $this->getAccount($id, $request, $response, 'api.registration.ticket.put');
        if (is_array($aid)) {
            return $aid;
        }

        $body = $request->getParsedBody();
        $set = [];

        if (array_key_exists('badgeName', $body)) {
            $set[] = '`BadgeName` = '.\MyPDO::quote($body['badgeName']);
        }
        if (array_key_exists('contact', $body)) {
            $set[] = '`EmergencyContact ` = '.\MyPDO::quote($body['contact']);
        }

        if (array_key_exists('note', $body)) {
            $set[] = '`Note` = '.\MyPDO::quote($body['note']);
        }

        if (empty($set)) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Unprocessable Entity', 'Understood Parameter missing.', 422)];
        }

        $setStr = implode(', ', $set);

        $sql = "UPDATE `Registrations` SET $setStr WHERE RegistrationID = $id";

        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        if ($sth->rowCount() == 0) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Conflict', 'Could not update', 409)];
        }

        $target = new GetTicket($this->container);
        $newdata = $target->buildResource($request, $response, $params)[1];
        $data = $target->arrayResponse($request, $response, $newdata);

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $data
        ];

    }


    public function processIncludes(Request $request, Response $response, $args, $values, &$data)
    {
        $this->ticketIncludes($request, $response, $args, $values, $data);

    }


    /* end PutTicket */
}
