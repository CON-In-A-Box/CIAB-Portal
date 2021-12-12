<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Delete(
 *      tags={"cycles"},
 *      path="/cycle/{id}",
 *      summary="Deletes an cycle",
 *      @OA\Parameter(
 *          description="Id of the cycle",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Response(
 *          response=204,
 *          description="OK"
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/cycle_not_found"
 *      ),
 *      security={
 *          {"ciab_auth": {}}
 *       }
 *  )
 **/

namespace App\Controller\Cycle;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Delete;

class DeleteCycle extends BaseCycle
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $this->getCycle($request, $response, $params);
        $permissions = ['api.delete.cycle'];
        $this->checkPermissions($permissions);

        $delete = Delete::new($this->container->db);
        $result = $delete->from('AnnualCycles')->whereEquals(['AnnualCycleID' => $params['id']])->perform();

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        [null],
        204
        ];

    }


    /* end DeleteCycle */
}
