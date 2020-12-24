<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\registration\Controller\Ticket;

use Slim\Http\Request;
use Slim\Http\Response;

class PrintQueueClaim extends BaseTicket
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        if (!\ciab\RBAC::havePermission('api.registration.ticket.print')) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }

        $id = $params['id'];
        $sql = "UPDATE `Registrations` SET `PrintRequested` = NULL, `LastPrintedDate` = NOW() WHERE `RegistrationID` = $id AND `PrintRequested` IS NOT NULL";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        if ($sth->rowCount() == 0) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Conflict', 'Not in Print Queue.', 409)];
        }

        $target = new GetTicket($this->container);
        $newdata = $target->buildResource($request, $response, $params)[1];
        $ticket = $target->arrayResponse($request, $response, $newdata);
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $ticket
        ];

    }


    public function processIncludes(Request $request, Response $response, $args, $values, &$data)
    {
        $this->ticketIncludes($request, $response, $args, $values, $data);

    }


    /* end PrintQueueClaim */
}
