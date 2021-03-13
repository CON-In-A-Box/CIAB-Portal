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
 *          name="showVoid",
 *          required=false,
 *          @OA\Schema(type="integer", enum={0,1})
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/ticket_includes"
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
        $sql = "SELECT * FROM `Registrations` WHERE  RegistrationID = $id";

        $query = $request->getQueryParams();
        if (!array_key_exists('showVoid', $query) || !boolval($query['showVoid'])) {
            $sql .= " AND `VoidDate` IS NULL";
        }

        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll();
        if (empty($data)) {
            throw new NotFoundException('Ticket Not Found');
        }

        $ticket = $this->buildTicket($data[0], $data[0]);

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $ticket
        ];

    }


    /* end GetTicket */
}
