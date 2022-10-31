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
use Atlas\Query\Update;

use App\Controller\NotFoundException;
use App\Controller\InvalidParameterException;

class VoidTicket extends BaseTicket
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.registration.ticket.void'];
        $this->checkPermissions($permissions);

        $id = $params['id'];
        $required = ['reason'];
        $body = $this->checkRequiredBody($request, $required);
        $user = $request->getAttribute('oauth2-token')['user_id'];
        $result = Update::new($this->container->db)
            ->table('Registrations')
            ->columns(['VoidBy' => $user, 'VoidReason' => $body['reason']])
            ->set('VoidDate', 'NOW()')
            ->whereEquals(['RegistrationID' => $id])
            ->perform();
        if ($result->rowCount() == 0) {
            throw new NotFoundException('Ticket Not Found');
        }

        return [null];

    }


    /* end VoidTicket */
}
