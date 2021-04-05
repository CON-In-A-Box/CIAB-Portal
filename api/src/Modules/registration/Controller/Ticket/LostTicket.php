<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Put(
 *      tags={"registration"},
 *      path="/registration/ticket/{id}/lost",
 *      summary="Report the badge for the ticket lost.",
 *      @OA\Parameter(
 *          description="Id of the ticket",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="OK",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/ticket"
 *          )
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

class LostTicket extends BaseTicket
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $update = Update::new($this->container->db)
            ->table('Registrations')
            ->set('BadgesPickedUp', '`BadgesPickedUp` + 1')
            ->whereEquals(['RegistrationID' => $params['id'], 'VoidDate' => null]);
        return $this->updateAndPrintTicket(
            $request,
            $response,
            $params,
            $params['id'],
            'api.registration.ticket.lost',
            $update,
            'Lost Ticket report Failed.'
        );

    }


    /* end LostTicket */
}
