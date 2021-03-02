<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\registration\Controller\Ticket;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class BaseTicketInclude extends BaseTicket
{


    public function __construct(Container $container)
    {
        parent::__construct($container);

    }


    public function processIncludes(Request $request, Response $response, $args, $values, &$data)
    {
        if (empty($values)) {
            return;
        }
        if (in_array('ticketType', $values)) {
            $target = new GetTicketTypes($this->container);
            $newargs = $args;
            $newargs['id'] = $data['ticketType'];
            $newdata = $target->buildResource($request, $response, $newargs)[1];
            $target->processIncludes($request, $response, $args, $values, $newdata);
            $data['ticketType'] = $target->arrayResponse($request, $response, $newdata);
        }
        if (in_array('member', $values)) {
            $target = new \App\Controller\Member\GetMember($this->container);
            $newargs = $args;
            $newargs['id'] = $data['member'];
            $newdata = $target->buildResource($request, $response, $newargs)[1];
            $target->processIncludes($request, $response, $args, $values, $newdata);
            $data['member'] = $target->arrayResponse($request, $response, $newdata);
        }
        if (in_array('registeredBy', $values)) {
            $target = new \App\Controller\Member\GetMember($this->container);
            $newargs = $args;
            $newargs['id'] = $data['registeredBy'];
            $newdata = $target->buildResource($request, $response, $newargs)[1];
            $target->processIncludes($request, $response, $args, $values, $newdata);
            $data['registeredBy'] = $target->arrayResponse($request, $response, $newdata);
        }
        if (in_array('badgeDependentOn', $values) && !empty($data['badgeDependentOn'])) {
            $target = new \App\Controller\Member\GetMember($this->container);
            $newargs = $args;
            $newargs['id'] = $data['badgeDependentOn'];
            $newdata = $target->buildResource($request, $response, $newargs)[1];
            $target->processIncludes($request, $response, $args, $values, $newdata);
            $data['badgeDependentOn'] = $target->arrayResponse($request, $response, $newdata);
        }
        if (in_array('event', $values)) {
            $target = new \App\Controller\Event\GetEvent($this->container);
            $newargs = $args;
            $newargs['id'] = $data['event'];
            $newdata = $target->buildResource($request, $response, $newargs)[1];
            $target->processIncludes($request, $response, $args, $values, $newdata);
            $data['event'] = $target->arrayResponse($request, $response, $newdata);
        }

    }


    /* End BaseTicketInclude */
}
