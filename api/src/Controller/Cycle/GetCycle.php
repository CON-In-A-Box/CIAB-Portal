<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Get(
 *      tags={"cycles"},
 *      path="/cycle/{id}",
 *      summary="Gets an cycle",
 *      @OA\Parameter(
 *          description="Id of the cycle",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Cycle found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/cycle"
 *          ),
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
use App\Controller\NotFoundException;

class GetCycle extends BaseCycle
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $cycle = $this->getCycle($params)[0];
        $cycle['id'] = $cycle['AnnualCycleID'];
        $this->id = $params['id'];
        unset($cycle['AnnualCycleID']);
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $cycle
        ];

    }


    /* end GetCycle */
}
