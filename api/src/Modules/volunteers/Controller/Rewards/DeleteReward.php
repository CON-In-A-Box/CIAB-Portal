<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Delete(
 *      tags={"volunteers"},
 *      path="/volunteers/rewards/{id}",
 *      summary="Deletes an existing volunteer reward",
 *      @OA\Parameter(
 *          description="Id of the volunteer reward",
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
 *          description="Entry not found in the system.",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/error"
 *          )
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Modules\volunteers\Controller\Rewards;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Delete;

class DeleteReward extends BaseReward
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.delete.volunteers'];
        $this->checkPermissions($permissions);

        Delete::new($this->container->db)
            ->from('VolunteerRewards')
            ->WhereEquals(['PrizeID' => $params['id']])
            ->perform();

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        [null],
        204
        ];

    }


    /* end DeleteRewards */
}
