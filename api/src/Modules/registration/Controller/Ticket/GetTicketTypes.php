<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"registration"},
 *      path="/registration/ticket/type/{id}",
 *      summary="Gets a ticket type for an event",
 *      deprecated=true,
 *      @OA\Parameter(
 *          description="Id of the ticket type",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/event",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Ticket type found",
 *          @OA\JsonContent(
 *              oneOf={
 *                  @OA\Schema(
 *                      ref="#/components/schemas/ticket_type"
 *                  ),
 *                  @OA\Schema(
 *                      ref="#/components/schemas/ticket_type_list"
 *                  )
 *              }
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
 *
 *  @OA\Get(
 *      tags={"registration"},
 *      path="/registration/ticket/type",
 *      summary="List all ticket types for the event",
 *      deprecated=true,
 *      @OA\Parameter(
 *          ref="#/components/parameters/event",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/max_results",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/page_token",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
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
use Atlas\Query\Select;
use App\Controller\IncludeResource;
use App\Error\NotFoundException;

class GetTicketTypes extends BaseTicket
{

    protected static $columnsToAttributes = [
    '"ticket_type"' => 'type',
    'BadgeTypeID' => 'id',
    'EventID' => 'event',
    'AvailableFrom' => 'available_from',
    'AvailableTo' => 'available_to',
    'Cost' => 'cost',
    'Name' => 'name',
    'BackgroundImage' => 'background_image'
    ];


    public function __construct($container)
    {
        parent::__construct($container);
        $this->includes = [
        new IncludeResource('\App\Controller\Event\GetEvent', 'id', 'event')
        ];

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $event = $this->getEventId($request);
        $select = Select::new($this->container->db)
            ->columns(...GetTicketTypes::selectMapping())
            ->from('BadgeTypes')
            ->whereEquals(['EventID' => $event]);
        if (array_key_exists('id', $params)) {
            $select->whereEquals(['BadgeTypeID' => $params['id']]);
        }

        $badges = $select->fetchAll();
        if (empty($badges)) {
            throw new NotFoundException('Badge Type Not Found');
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


    /* end GetTicketTypes */
}
