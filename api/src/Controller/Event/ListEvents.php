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
 *                  enum={"departmentId","postedBy"}
 *              )
 *          )
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/maxResults",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/pageToken",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="OK",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/event_list"
 *          )
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

use App\Controller\InvalidParameterException;

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


        $sql = "SELECT * FROM `Events`";
        if ($begin !== null) {
            $sql .= " WHERE `DateFrom` >= '".date("Y-m-d", $begin)."'";
        }
        if ($end !== null) {
            if ($begin === null) {
                $sql .= " WHERE";
            } else {
                $sql .= " AND";
            }
            $sql .= " DateTo <= '".date("Y-m-d", $end)."'";
        }
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $events = $sth->fetchAll();
        $output = array();
        $output['type'] = 'event_list';
        $data = array();
        foreach ($events as $entry) {
            $data[] = $this->processEvent($entry);
        }
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        $output];

    }


    /* end ListEvents */
}
