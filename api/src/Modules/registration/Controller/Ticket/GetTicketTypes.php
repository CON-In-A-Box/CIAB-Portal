<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"registration"},
 *      path="/registration/ticket/type/{id}/{event}",
 *      summary="Gets a ticket type for an event",
 *      @OA\Parameter(
 *          description="Id of the ticket type",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Parameter(
 *          description="Id of the event",
 *          in="path",
 *          name="event",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Ticket type found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/ticket_type"
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
 *
 *  @OA\Get(
 *      tags={"registration"},
 *      path="/registration/ticket/type/{id}",
 *      summary="Gets a ticket type for the current event",
 *      @OA\Parameter(
 *          description="Id of the ticket type",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Ticket type found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/ticket_type"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/ticket_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 *
 *  @OA\Get(
 *      tags={"registration"},
 *      path="/registration/ticket/type",
 *      summary="List all ticket types for the current event",
 *      @OA\Response(
 *          response=200,
 *          description="Ticket type found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/ticket_type_list"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Modules\registration\Controller\Ticket;

use Slim\Http\Request;
use Slim\Http\Response;

use App\Controller\NotFoundException;

class GetTicketTypes extends BaseTicket
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $sql = "SELECT * FROM `BadgeTypes` ";
        $conditional = [];
        if (array_key_exists('event', $params)) {
            $conditional[] = '`EventID` = '.$params['event'];
        }
        if (array_key_exists('id', $params)) {
            $conditional[] = '`BadgeTypeID` = '.$params['id'];
        }
        if (!empty($conditional)) {
            $sql .= 'WHERE '.implode(' AND ', $conditional);
        }

        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll();
        if (empty($data)) {
            throw new NotFoundException('Badge Type Not Found');
        }
        $badges = [];
        foreach ($data as $entry) {
            $badge = $entry;
            $badge['id'] = $entry['BadgeTypeID'];
            unset($badge['BadgeTypeID']);
            $badge['event'] = $entry['EventID'];
            unset($badge['EventID']);
            $badge['type'] = 'ticket_type';
            $badges[] = $badge;
        }

        if (count($badges) > 1) {
            $output = ['type' => 'ticket_type_list'];
            return [
            \App\Controller\BaseController::LIST_TYPE,
            $badges,
            $output
            ];
        } else {
            return [
            \App\Controller\BaseController::RESOURCE_TYPE,
            $badges[0]
            ];
        }

    }


    public function processIncludes(Request $request, Response $response, $args, $values, &$data)
    {
        if (in_array('event', $values)) {
            $target = new \App\Controller\Event\GetEvent($this->container);
            $newargs = $args;
            $newargs['id'] = $data['event'];
            $newdata = $target->buildResource($request, $response, $newargs)[1];
            $target->processIncludes($request, $response, $args, $values, $newdata);
            $data['event'] = $target->arrayResponse($request, $response, $newdata);
        }

    }


    /* end GetTicketTypes */
}
