<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Put(
 *      tags={"registration"},
 *      path="/registration/ticket/{id}/checkin",
 *      summary="Check in a ticket and generate boarding pass",
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
 *          ),
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

class CheckinTicket extends BaseTicket
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $id = $params['id'];
        $sql = "UPDATE `Registrations` SET `BoardingPassGenerated` = NOW() WHERE `RegistrationID` = $id AND `BoardingPassGenerated` IS NULL AND `VoidDate` IS NULL";
        return $this->updateAndPrintTicket(
            $request,
            $response,
            $params,
            $id,
            'api.registration.ticket.checkin',
            $sql,
            'Generation of Boarding Pass Failed.'
        );

    }


    /* end CheckinTicket */
}
