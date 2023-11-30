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
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="badge_id",
 *                      type="string",
 *                  ),
 *                  @OA\Property(
 *                      property="badge_name",
 *                      type="string",
 *                  ),
 *                  @OA\Property(
 *                      property="emergency_contact",
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
use Atlas\Query\Update;

use App\Error\ConflictException;
use App\Error\InvalidParameterException;

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

        if (array_key_exists('badge_name', $body)) {
            $set['BadgeName'] = $body['badge_name'];
        }
        if (array_key_exists('emergency_contact', $body)) {
            $set['EmergencyContact'] = $body['emergency_contact'];
        }
        if (array_key_exists('badge_id', $body)) {
            $this->verifyBadgeId($id, $body['badge_id']);
            $set['BadgeID'] = $body['badge_id'];
        }

        if (array_key_exists('note', $body)) {
            $set['Note'] = $body['note'];
        }

        if (empty($set)) {
            throw new InvalidParameterException('Understood Parameter missing.');
        }


        $result = Update::new($this->container->db)
            ->table('Registrations')
            ->columns($set)
            ->whereEquals(['RegistrationID' => $id])
            ->perform();
        if ($result->rowCount() == 0) {
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
