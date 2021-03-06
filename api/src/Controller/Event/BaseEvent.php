<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

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


    /* End BaseEvent */
}
