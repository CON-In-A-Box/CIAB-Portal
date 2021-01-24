<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Event;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\BaseController;

abstract class BaseEvent extends BaseController
{

    /**
     * @var int
     */
    protected $id = 0;


    public function __construct(Container $container)
    {
        parent::__construct('event', $container);

    }


    protected function buildEventHateoas(Request $request)
    {
        if ($this->id !== 0) {
            $path = $request->getUri()->getBaseUrl();
            $this->addHateoasLink('self', $path.'/event/'.strval($this->id), 'GET');
        }

    }


    protected function processEvent($item)
    {
        $newEvent['type'] = 'event';
        $newEvent['id'] = $item['EventID'];
        $newEvent['cycle'] = $item['AnnualCycleID'];
        $newEvent['dateFrom'] = $item['DateFrom'];
        $newEvent['dateTo'] = $item['DateTo'];
        $newEvent['name'] = $item['EventName'];
        return $newEvent;

    }


    public function processIncludes(Request $request, Response $response, $args, $values, &$data)
    {
        if (in_array('cycle', $values)) {
            $target = new \App\Controller\Cycle\GetCycle($this->container);
            $newargs = $args;
            $newargs['id'] = $data['cycle'];
            $newdata = $target->buildResource($request, $response, $newargs)[1];
            $target->processIncludes($request, $response, $args, $values, $newdata);
            $data['cycle'] = $target->arrayResponse($request, $response, $newdata);
        }

    }


    /* End BaseEvent */
}
