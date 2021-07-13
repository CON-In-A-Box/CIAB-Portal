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
 *                  ref="#/components/schemas/cycle"
 *              ),
 *              @OA\Schema(
 *                  type="integer",
 *                  description="Cycle Id"
 *              )
 *          }
 *      ),
 *      @OA\Property(
 *          property="date_from",
 *          type="string",
 *          format="date",
 *          description="Date the event starts"
 *      ),
 *      @OA\Property(
 *          property="date_to",
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
use App\Controller\InvalidParameterException;

abstract class BaseEvent extends BaseController
{

    /**
     * @var int
     */
    protected $id = 0;

    protected static $columnsToAttributes = [
    '"event"' => 'type',
    'EventID' => 'id',
    'AnnualCycleID' => 'cycle',
    'DateFrom' => 'date_from',
    'DateTo' => 'date_to',
    'EventName' => 'name'
    ];


    public function __construct(Container $container)
    {
        parent::__construct('event', $container);
        $this->includes = [
        new IncludeResource('\App\Controller\Cycle\GetCycle', 'id', 'cycle')
        ];

    }


    private function handleDate($request, $response, $params, &$body, $dateName)
    {
        if (array_key_exists($dateName, $body)) {
            try {
                $body[$dateName] = date_format(new \DateTime($body[$dateName]), 'Y-m-d');

                $target = new \App\Controller\Cycle\ListCycles($this->container);
                $newrequest = $request->withQueryParams(['includesDate' => $body[$dateName]]);
                $data = $target->buildResource($newrequest, $response, $params)[1];
                if (empty($data)) {
                    throw new InvalidParameterException("No existing cycle contains '$dateName'.");
                }
                $cycle = $data[0]['id'];
                return $cycle;
            } catch (\Exception $e) {
                throw new InvalidParameterException("'$dateName' parameter not valid");
            }
        }

        return null;

    }


    protected function buildPutPostBody($request, $response, $params, $required, $event)
    {
        $body = $this->checkRequiredBody($request, $required);
        unset($body['cycle']);

        if ($event) {
            $cycle_from = $event['cycle'];
            $cycle_to = $event['cycle'];
        } else {
            $cycle_from = null;
            $cycle_to = null;
        }
        $cycle = $this->handleDate($request, $response, $params, $body, 'date_from');
        if ($cycle != null) {
            $cycle_from = $cycle;
        }
        $cycle = $this->handleDate($request, $response, $params, $body, 'date_to');
        if ($cycle != null) {
            $cycle_to = $cycle;
        }
        if (!$cycle_to || !$cycle_from || $cycle_to != $cycle_from) {
            throw new InvalidParameterException("Event dates not allowed to span cycles ($cycle_from - $cycle_to).");
        }

        $body['cycle'] = $cycle_from;

        return $body;

    }


    /* End BaseEvent */
}
