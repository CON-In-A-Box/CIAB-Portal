<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Delete(
 *      tags={"volunteers"},
 *      path="/volunteers/reward_group/{id}",
 *      summary="Deletes an existing volunteer reward group",
 *      @OA\Parameter(
 *          description="Id of the volunteer reward group",
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
use Atlas\Query\Update;

class DeleteRewardGroup extends BaseRewardGroup
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.delete.volunteers'];
        $this->checkPermissions($permissions);

        Update::new($this->container->db)
            ->table('VolunteerRewards')
            ->column('RewardGroupID', null)
            ->WhereEquals(['RewardGroupID' => $params['id']])
            ->perform();

        Delete::new($this->container->db)
            ->from('RewardGroup')
            ->WhereEquals(['RewardGroupID' => $params['id']])
            ->perform();

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        [null],
        204
        ];

    }


    /* end DeleteRewardGroup */
}
