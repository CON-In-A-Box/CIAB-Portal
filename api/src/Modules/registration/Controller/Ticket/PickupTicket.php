<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Put(
 *      tags={"registration"},
 *      path="/registration/ticket/{id}/pickup",
 *      summary="Report the badge for the ticket as picked up.",
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
 *          @OA\MediaType(
 *              mediaType="text/html"
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

class PickupTicket extends BaseTicket
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $id = $params['id'];
        $aid = $this->getAccount($id, $request, $response, 'api.registration.ticket.pickup');
        if (is_array($aid)) {
            return $aid;
        }

        $update = Update::new($this->container->db)
            ->table('Registrations')
            ->columns(['BadgesPickedUp' => 1])
            ->whereEquals(['RegistrationID' => $id, 'VoidDate' => null]);

        $rc = $this->updateTicket(
            $request,
            $response,
            $params,
            null,
            $update,
            'Pickup Ticket report Failed.',
            false
        );
        if ($rc == null) {
            return [null];
        }
        return $rc;

    }


    /* end PickupTicket */
}
