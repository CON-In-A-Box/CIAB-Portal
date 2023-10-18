<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Parameter(
 *      parameter="show_void",
 *      description="Show voided tickets as well.",
 *      in="query",
 *      name="show_void",
 *      required=false,
 *      @OA\Schema(type="integer", enum={0,1})
 *  )
 *
 *  @OA\Parameter(
 *      parameter="show_checked_in",
 *      description="Show/exclude checked in",
 *      in="query",
 *      name="checked_in",
 *      required=false,
 *      @OA\Schema(type="integer", enum={0,1})
 *  )
 *
 *  @OA\Parameter(
 *      parameter="show_picked_up",
 *      description="Show/exclude picked up",
 *      in="query",
 *      name="picked_up",
 *      required=false,
 *      @OA\Schema(type="integer", enum={0,1})
 *  )
 *
 *  @OA\Get(
 *      tags={"registration"},
 *      path="/registration/ticket/list/unclaimed",
 *      summary="Gets all unclaimed tickets for an event",
 *      @OA\Parameter(
 *          ref="#/components/parameters/event",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/show_void",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/show_checked_in",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/max_results",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/page_token",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Tickets found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/ticket_list"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/event_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 *
 *  @OA\Get(
 *      tags={"registration"},
 *      path="/registration/ticket/list/{member}",
 *      summary="Gets tickets for an event for a member",
 *      @OA\Parameter(
 *          description="The member",
 *          in="path",
 *          name="member",
 *          required=true,
 *          @OA\Schema(
 *              oneOf = {
 *                  @OA\Schema(
 *                      description="Member email",
 *                      type="string"
 *                  ),
 *                  @OA\Schema(
 *                      description="Member id",
 *                      type="integer"
 *                  )
 *              }
 *          )
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/event",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/show_void",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/show_checked_in",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/show_picked_up",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/max_results",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/page_token",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Tickets found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/ticket_list"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/event_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 *
 *  @OA\Get(
 *      tags={"registration"},
 *      path="/registration/ticket/list",
 *      summary="Gets tickets for the current member for the event",
 *      @OA\Parameter(
 *          ref="#/components/parameters/event",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/show_void",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/show_checked_in",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/show_picked_up",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/max_results",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/page_token",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Tickets found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/ticket_list"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/event_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Modules\registration\Controller\Ticket;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;

use App\Controller\PermissionDeniedException;
use App\Controller\NotFoundException;

class ListTickets extends BaseTicketInclude
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $user = $request->getAttribute('oauth2-token')['user_id'];

        if (array_key_exists('member', $params)) {
            if ($params['member'] != 'unclaimed') {
                $aid = $this->getMember($request, $params['member'], 'id')[0]['id'];
            } else {
                $aid = 'unclaimed';
            }
        } else {
            $aid = $user;
        }

        if ($user != $aid) {
            $permissions = ['api.registration.ticket.list'];
            $this->checkPermissions($permissions);
        }

        $select = Select::new($this->container->db)
            ->columns(...BaseTicket::selectMapping())
            ->from('Registrations')
            ->where(' (');
        if ($aid !== 'unclaimed') {
            $select->catWhere(' RegisteredByID = ', $aid)
                ->catWhere(' OR AccountID = ', $aid);
        } else {
            $select->catWhere(' BadgesPickedUp < 1')
                ->catWhere(' OR BadgesPickedUp IS NULL');
        }
        $select->catWhere(') ');
        $event = $this->getEventId($request);
        $select->whereEquals(['EventID' => $event]);
        $query = $request->getQueryParams();
        if (!array_key_exists('show_void', $query) || !boolval($query['show_void'])) {
            $select->whereEquals(['VoidDate' => null]);
        }
        if (array_key_exists('checked_in', $query)) {
            if (boolval($query['checked_in'])) {
                $select->where('BoardingPassGenerated IS NOT NULL');
            } else {
                $select->whereEquals(['BoardingPassGenerated' => null]);
            }
        }
        if (array_key_exists('picked_up', $query)) {
            if (boolval($query['picked_up'])) {
                $select->where('BadgesPickedUp > 0');
            } else {
                $select->where('BadgesPickedUp = 0');
            }
        }
        $tickets = $select->fetchAll();
        if (empty($tickets)) {
            throw new NotFoundException('Ticket Not Found');
        }

        $output = ['type' => 'ticket_list'];
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $tickets,
        $output
        ];

    }


    /* end ListTickets */
}
