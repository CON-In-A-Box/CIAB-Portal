<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Put(
 *      tags={"registration"},
 *      path="/registration/ticket/{id}",
 *      summary="Updates ticket information.",
 *      @OA\Parameter(
 *          description="Id of the ticket",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/ticket_includes"
 *      ),
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="badgeName",
 *                      type="string",
 *                  ),
 *                  @OA\Property(
 *                      property="contact",
 *                      type="string",
 *                  ),
 *                  @OA\Property(
 *                      property="note",
 *                      type="string",
 *                  )
 *              )
 *          )
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

use App\Controller\ConflictException;
use App\Controller\InvalidParameterException;

class PutTicket extends BaseTicketInclude
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $id = $params['id'];
        $aid = $this->getAccount($id, $request, $response, 'api.registration.ticket.put');

        $body = $request->getParsedBody();
        if (empty($body)) {
            throw new InvalidParameterException('Body not present');
        }
        $set = [];

        if (array_key_exists('badgeName', $body)) {
            $set[] = '`BadgeName` = '.\MyPDO::quote($body['badgeName']);
        }
        if (array_key_exists('contact', $body)) {
            $set[] = '`EmergencyContact` = '.\MyPDO::quote($body['contact']);
        }

        if (array_key_exists('note', $body)) {
            $set[] = '`Note` = '.\MyPDO::quote($body['note']);
        }

        if (empty($set)) {
            throw new InvalidParameterException('Understood Parameter missing.');
        }

        $setStr = implode(', ', $set);

        $sql = "UPDATE `Registrations` SET $setStr WHERE RegistrationID = $id";

        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        if ($sth->rowCount() == 0) {
            throw new ConflictException('Could not update');
        }

        $target = new GetTicket($this->container);
        $newdata = $target->buildResource($request, $response, $params)[1];
        $data = $target->arrayResponse($request, $response, $newdata);

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $data
        ];

    }


    /* end PutTicket */
}
