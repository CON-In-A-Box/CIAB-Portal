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

class DeleteCycle extends BaseCycle
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $this->getCycle($params);
        $permissions = ['api.delete.cycle'];
        $this->checkPermissions($permissions);

        $sth = $this->container->db->prepare("DELETE FROM `AnnualCycles` WHERE `AnnualCycleID` = ".$params['id'].";");
        $sth->execute();
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        [null],
        204
        ];

    }


    /* end DeleteCycle */
}
