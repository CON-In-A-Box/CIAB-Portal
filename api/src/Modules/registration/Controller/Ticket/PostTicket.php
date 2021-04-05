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
 *                      property="ticketType",
 *                      type="string",
 *                  ),
 *                  @OA\Property(
 *                      property="dependOn",
 *                      type="string",
 *                  ),
 *                  @OA\Property(
 *                      property="badgeName",
 *                      type="string",
 *                  ),
 *                  @OA\Property(
 *                      property="contact",
 *                      type="string",
 *                  ),
 *                  @OA\Property(
 *                      property="registeredBy",
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

        $required = ['member', 'ticketType'];
        $body = $this->checkRequiredBody($request, $required);

        $data = $this->findMember($request, $response, $body, 'member');
        $member = $data['id'];
        $body['member'] = $member;

        if (array_key_exists('event', $body)) {
            $event = $body['event'];
        } else {
            $event = 'current';
        }
        $body['event'] = $this->getEvent($event)['id'];

        $target = new GetTicketTypes($this->container);
        $target->buildResource($request, $response, ['id' => $body['ticketType']])[1];

        if (array_key_exists('dependOn', $body)) {
            $data = $this->findMember($request, $response, $body, 'dependOn');
            $body['dependOn'] = $data['id'];
        }

        if (array_key_exists('registeredBy', $body)) {
            $data = $this->findMember($request, $response, $body, 'registeredBy');
            $body['registeredBy'] = $data['id'];
        } else {
            $body['registeredBy'] = $member;
        }

        $insert = Insert::new($this->container->db);
        $insert->into('Registrations');
        $insert->columns(BaseTicket::insertPayloadFromParams($body, false));
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
