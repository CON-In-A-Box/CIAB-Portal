<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Get(
 *      tags={"cycles"},
 *      path="/cycle",
 *      summary="Lists cycles",
 *      @OA\Parameter(
 *          description="Start date for cycle list",
 *          in="query",
 *          name="from",
 *          required=false,
 *          @OA\Schema(
 *              type="string",
 *              format="date"
 *          )
 *      ),
 *      @OA\Parameter(
 *          description="Final date for cycle list",
 *          in="query",
 *          name="to",
 *          required=false,
 *          @OA\Schema(
 *              type="string",
 *              format="date"
 *          )
 *      ),
 *      @OA\Parameter(
 *          description="A date that must be included in cycles in the list",
 *          in="query",
 *          name="includesDate",
 *          required=false,
 *          @OA\Schema(
 *              type="string",
 *              format="date"
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
 *              ref="#/components/schemas/cycle_list"
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/cycle_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Controller\Cycle;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;

use App\Controller\InvalidParameterException;

class ListCycles extends BaseCycle
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $begin = $request->getQueryParam('begin', null);
        $end = $request->getQueryParam('end', null);
        $includesDate = $request->getQueryParam('includesDate', null);

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
        if ($includesDate !== null) {
            $includesDate = strtotime($includesDate);
            if (!$includesDate) {
                throw new InvalidParameterException('\'includesDate\' parameter not valid');
            }
        }

        $condition = false;

        $select = Select::new($this->container->db);
        $select->columns(...BaseCycle::selectMapping());
        $select->from('AnnualCycles');

        if ($begin !== null) {
            $select->where("`DateFrom` >= '".date("Y-m-d", $begin)."'");
        }
        if ($end !== null) {
            $select->where("DateTo <= '".date("Y-m-d", $end)."'");
        }
        if ($includesDate !== null) {
            $target = date("Y-m-d", $includesDate);
            $select->where("(DateFrom <= '$target' AND DateTo >= '$target')");
        }
        $data = $select->fetchAll();
        $output = array();
        $output['type'] = 'cycle_list';
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        $output];

    }


    /* end ListCycles */
}
