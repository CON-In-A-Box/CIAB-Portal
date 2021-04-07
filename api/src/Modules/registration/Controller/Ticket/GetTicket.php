<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"registration"},
 *      path="/registration/ticket/{id}",
 *      summary="Gets a registration ticket",
 *      @OA\Parameter(
 *          description="Id of the ticket.",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Parameter(
 *          description="Show voided tickets as well.",
 *          in="query",
 *          name="show_void",
 *          required=false,
 *          @OA\Schema(type="integer", enum={0,1})
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Ticket found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/ticket"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/member_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Modules\registration\Controller\Ticket;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;

use App\Controller\NotFoundException;

class GetTicket extends BaseTicketInclude
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $id = $params['id'];
        $aid = $this->getAccount($id, $request, $response, 'api.registration.ticket.get');
        if (is_array($aid)) {
            return $aid;
        }
        $select = Select::new($this->container->db)
            ->columns(...BaseTicket::selectMapping())
            ->from('Registrations')
            ->whereEquals(['RegistrationID' => $id]);

        $query = $request->getQueryParams();
        if (!array_key_exists('show_void', $query) || !boolval($query['show_void'])) {
            $select->whereEquals(['VoidDate' => null]);
        }

        $ticket = $select->fetchOne();
        if (empty($ticket)) {
            throw new NotFoundException('Ticket Not Found');
        }

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $ticket
        ];

    }


    /* end GetTicket */
}
