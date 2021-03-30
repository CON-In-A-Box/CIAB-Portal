<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Put(
 *      tags={"registration"},
 *      path="/registration/ticket/{id}/void",
 *      summary="Void a ticket.",
 *      @OA\Parameter(
 *          description="Id of the ticket",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="OK"
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=409,
 *          description="Update Conflict",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/error"
 *          )
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/ticket_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Modules\registration\Controller\Ticket;

use Slim\Http\Request;
use Slim\Http\Response;

use App\Controller\NotFoundException;
use App\Controller\InvalidParameterException;

class VoidTicket extends BaseTicket
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.registration.ticket.void'];
        $this->checkPermissions($permissions);

        $id = $params['id'];
        $body = $request->getParsedBody();
        if (empty($body) || !array_key_exists('reason', $body)) {
            throw new InvalidParameterException('Required \'reason\' parameter not present');
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
