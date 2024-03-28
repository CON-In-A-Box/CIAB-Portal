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

use App\Error\NotFoundException;

class GetTicket extends BaseTicketInclude
{


    private static function convertDate($data, $field): ?string
    {
        if ($data[$field]) {
            $datetime = \DateTime::createFromFormat('Y-m-d H:i:s', $data[$field]);
            return $datetime->format(\DateTime::RFC3339);
        }
        return null;

    }


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
        $ticket['type'] = 'ticket';

        $ticket['registration_date'] = $this->convertDate($ticket, 'registration_date');
        $ticket['boarding_pass_generated'] = $this->convertDate($ticket, 'boarding_pass_generated');
        $ticket['print_requested'] = $this->convertDate($ticket, 'print_requested');
        $ticket['void_date'] = $this->convertDate($ticket, 'void_date');
        $ticket['last_printed_date'] = $this->convertDate($ticket, 'last_printed_date');

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $ticket
        ];

    }


    /* end GetTicket */
}
