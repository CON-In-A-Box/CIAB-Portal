<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\registration\Controller\Ticket;

require_once __DIR__.'/../../../../../../backends/email.inc';

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views;

class PrintBadge extends BaseTicket
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.registration.ticket.print'];
        $this->checkPermissions($permissions);

        $id = $params['id'];
        $this->verifyTicket($id);
        $this->printBadge($request, $id);

        return [null];

    }


    /* end PrintBadge */
}
