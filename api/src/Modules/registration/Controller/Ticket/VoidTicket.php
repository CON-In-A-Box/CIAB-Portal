<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\registration\Controller\Ticket;

use Slim\Http\Request;
use Slim\Http\Response;

use App\Controller\NotFoundException;

class VoidTicket extends BaseTicket
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.registration.ticket.void'];
        $this->checkPermissions($permissions);

        $id = $params['id'];
        $body = $request->getParsedBody();
        if (empty($body) || !array_key_exists('reason', $body)) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Required \'reason\' parameter not present', 'Missing Parameter', 400)];
        }
        $reason = \MyPDO::quote($body['reason']);
        $user = $request->getAttribute('oauth2-token')['user_id'];
        $sql = "UPDATE `Registrations` SET `VoidDate` = NOW(), `VoidBy` = $user, `VoidReason` = $reason WHERE RegistrationID = $id";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        if ($sth->rowCount() == 0) {
            throw new NotFoundException('Ticket Not Found');
        }

        return [null];

    }


    /* end VoidTicket */
}
