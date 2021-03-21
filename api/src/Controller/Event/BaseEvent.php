<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Tag(
 *      name="events",
 *      description="Features around events"
 *  )
 *
 *  @OA\Schema(
 *      schema="event",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"event"}
 *      ),
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          description="event Id"
 *      ),
 *      @OA\Property(
 *          property="cycle",
 *          description="Annual cycle this event is part of",
 *          oneOf={
 *              @OA\Schema(
 *                  type="integer",
 *                  description="Cycle Id"
 *              ),
 *              @OA\Schema(
 *                  ref="#/components/schemas/cycle"
 *              )
 *          }
 *      ),
 *      @OA\Property(
 *          property="dateFrom",
 *          type="string",
 *          format="date",
 *          description="Date the event starts"
 *      ),
 *      @OA\Property(
 *          property="dateTo",
 *          type="string",
 *          format="date",
 *          description="Date the event ends"
 *      ),
 *      @OA\Property(
 *          property="name",
 *          type="string",
 *          description="Name of the event"
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="event_list",
 *      allOf = {
 *          @OA\Schema(ref="#/components/schemas/resource_list")
 *      },
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"event_list"}
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="List of events",
 *          @OA\Items(
 *              ref="#/components/schemas/event"
 *          ),
 *      )
 *  )
 *
 *   @OA\Response(
 *      response="event_not_found",
 *      description="Event not found in the system.",
 *      @OA\JsonContent(
 *          ref="#/components/schemas/error"
 *      )
 *   )
 **/

namespace App\Controller\Event;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\BaseController;
use App\Controller\IncludeResource;

abstract class BaseEvent extends BaseController
{

    /**
     * @var int
     */
    protected $id = 0;


    public function __construct(Container $container)
    {
        parent::__construct('event', $container);
        $this->includes = [
        new IncludeResource('\App\Controller\Cycle\GetCycle', 'id', 'cycle')
        ];

    }


    /* End BaseEvent */
}
