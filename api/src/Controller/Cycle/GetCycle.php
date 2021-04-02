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
use Atlas\Query\Select;
use App\Controller\NotFoundException;

class GetCycle extends BaseCycle
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $select = Select::new($this->container->db);
        $select->columns(...BaseCycle::selectMapping());
        $select->from('AnnualCycles');
        $select->whereEquals(['AnnualCycleID' => $params['id']]);
        $data = $select->fetchOne();
        if (empty($data)) {
            throw new NotFoundException("Cycle '{$params['id']}' Not Found");
        }
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $data
        ];

    }


    /* end GetCycle */
}
