<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\registration\Controller\Ticket;

use Slim\Http\Request;
use Slim\Http\Response;

class DeleteTicket extends BaseTicket
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        if (!\ciab\RBAC::havePermission('api.registration.ticket.delete')) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }
        $id = $params['id'];
        $sql = "DELETE FROM `Registrations` WHERE RegistrationID = $id";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        if ($sth->rowCount() == 0) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Not Found', 'Ticket Not Found', 404)];
        }

        return [null];

    }


    /* end DeleteTicket */
}
