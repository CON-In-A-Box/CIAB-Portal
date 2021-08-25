<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Post(
 *      tags={"registration"},
 *      path="/registration/ticket",
 *      summary="Create a new Ticket",
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="member",
 *                      type="string",
 *                  ),
 *                  @OA\Property(
 *                      property="event",
 *                      type="string",
 *                  ),
 *                  @OA\Property(
 *                      property="ticket_type",
 *                      type="string",
 *                  ),
 *                  @OA\Property(
 *                      property="badge_dependent_on",
 *                      type="string",
 *                  ),
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
 *                      property="registered_by",
 *                      type="string",
 *                  )
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=201,
 *          description="Ticket created",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/ticket"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/event_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Modules\registration\Controller\Ticket;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Insert;

use App\Controller\InvalidParameterException;
use App\Controller\ConflictException;

class PostTicket extends BaseTicketInclude
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.registration.ticket.post'];
        $this->checkPermissions($permissions);

        $required = ['member', 'ticket_type'];
        $body = $this->checkRequiredBody($request, $required);

        $member = $this->getMember($request, $body['member'])[0]['id'];
        $body['member'] = $member;

        if (array_key_exists('event', $body)) {
            $event = $body['event'];
        } else {
            $event = 'current';
        }
        $body['event'] = $this->getEvent($event)['id'];

        $target = new GetTicketTypes($this->container);
        $target->buildResource($request, $response, ['id' => $body['ticket_type']])[1];

        if (array_key_exists('badge_dependent_on', $body)) {
            $body['badge_dependent_on'] = $this->getMember($request, $body['badge_dependent_on'])[0]['id'];
        }

        if (array_key_exists('registered_by', $body)) {
            $body['registered_by'] = $this->getMember($request, $body['registered_by'])[0]['id'];
        } else {
            $body['registered_by'] = $member;
        }

        if (array_key_exists('badge_id', $body)) {
            $this->verifyBadgeId(null, $body['badge_id'], $body['event']);
        }

        $insert = Insert::new($this->container->db)
            ->into('Registrations')
            ->columns(BaseTicket::insertPayloadFromParams($body, false))
            ->set('RegistrationDate', 'NOW()');
        $result = $insert->perform();
        if ($result->rowCount() == 0) {
            throw new ConflictException('Could not update');
        }

        $target = new GetTicket($this->container);
        $data = $target->buildResource($request, $response, ['id' => $insert->getLastInsertId()])[1];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data),
        201
        ];

    }


    /* end PostTicket */
}
