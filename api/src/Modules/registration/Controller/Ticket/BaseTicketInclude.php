<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *      @OA\Parameter(
 *          parameter="ticket_includes",
 *          description="Include the resource instead of the ID.",
 *          in="query",
 *          name="include",
 *          required=false,
 *          explode=false,
 *          style="form",
 *          @OA\Schema(
 *              type="array",
 *              @OA\Items(
 *                  type="string",
 *                  enum={"ticketType","member","badgeDependentOn",
 *                        "registeredBy", "event"}
 *              )
 *          )
 *      )
 **/


namespace App\Modules\registration\Controller\Ticket;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\IncludeResource;

abstract class BaseTicketInclude extends BaseTicket
{


    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->includes = [
        new IncludeResource('\App\Modules\registration\Controller\Ticket\GetTicketTypes', 'id', 'ticketType'),
        new IncludeResource('\App\Controller\Member\GetMember', 'id', 'member'),
        new IncludeResource('\App\Controller\Member\GetMember', 'id', 'registeredBy'),
        new IncludeResource('\App\Controller\Member\GetMember', 'id', 'badgeDependentOn'),
        new IncludeResource('\App\Controller\Event\GetEvent', 'id', 'event')
        ];

    }


    /* End BaseTicketInclude */
}
