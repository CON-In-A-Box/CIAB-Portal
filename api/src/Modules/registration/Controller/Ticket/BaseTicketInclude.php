<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/


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
        new IncludeResource('\App\Modules\registration\Controller\Ticket\GetTicketTypes', 'id', 'ticket_type'),
        new IncludeResource('\App\Controller\Member\GetMember', 'id', 'member'),
        new IncludeResource('\App\Controller\Member\GetMember', 'id', 'registered_by'),
        new IncludeResource('\App\Controller\Member\GetMember', 'id', 'badge_dependent_on'),
        new IncludeResource('\App\Controller\Member\GetMember', 'id', 'void_by'),
        new IncludeResource('\App\Controller\Event\GetEvent', 'id', 'event')
        ];

    }


    /* End BaseTicketInclude */
}
