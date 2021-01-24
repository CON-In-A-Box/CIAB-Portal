<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\registration\Controller\Ticket;

use Slim\Http\Request;
use Slim\Http\Response;

class UnvoidTicket extends BaseTicket
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        if (!\ciab\RBAC::havePermission('api.registration.ticket.unvoid')) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }
        $id = $params['id'];
        $sql = "UPDATE `Registrations` SET `VoidDate` = NULL, `VoidBy` = NULL, `VoidReason` = NULL WHERE RegistrationID = $id AND `VoidDate` IS NOT NULL";

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


    /* end UnvoidTicket */
}
