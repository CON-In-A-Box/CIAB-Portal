<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"events"},
 *      path="/event",
 *      summary="Lists events",
 *      @OA\Parameter(
 *          description="First date to search from.",
 *          in="query",
 *          name="begin",
 *          required=false,
 *          style="form",
 *          @OA\Schema(
 *              type="string",
 *              format="date"
 *          )
 *      ),
 *      @OA\Parameter(
 *          description="Last date to search until.",
 *          in="query",
 *          name="end",
 *          required=false,
 *          style="form",
 *          @OA\Schema(
 *              type="string",
 *              format="date"
 *          )
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
 *          description="OK",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/event_list"
 *          )
 *      ),
 *      @OA\Response(
 *          response=400,
 *          ref="#/components/responses/400"
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Controller\Event;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;

use App\Error\InvalidParameterException;

class ListEvents extends BaseEvent
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $begin = $request->getQueryParam('begin', null);
        $end = $request->getQueryParam('end', null);

        if ($begin !== null) {
            $begin = strtotime($begin);
            if (!$begin) {
                throw new InvalidParameterException('\'begin\' parameter not valid');
            }
        }
        if ($end !== null) {
            $end = strtotime($end);
            if (!$end) {
                throw new InvalidParameterException('\'end\' parameter not valid');
            }
        }

        $select = Select::new($this->container->db);
        $select->columns(...BaseEvent::selectMapping());
        $select->from('Events');

        if ($begin !== null) {
            $select->where("`DateFrom` >= '".date("Y-m-d", $begin)."'");
        }
        if ($end !== null) {
            $select->where("`DateTo` <= '".date("Y-m-d", $end)."'");
        }
        $events = $select->fetchAll();
        $output = array();
        $output['type'] = 'event_list';
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $events,
        $output];

    }


    /* end ListEvents */
}
