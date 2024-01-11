<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Delete(
 *      tags={"volunteers"},
 *      path="/volunteer/rewards/{id}",
 *      summary="Deletes or marks an existing volunteer reward retired",
 *      description="If the reward has never been distributed then it is deleted from the database.
 However, if that reward has been awarded then instead the reward is marked as retired and its inventory is set to the same as claimed, so effectively zero.",
 *      @OA\Parameter(
 *          description="Id of the volunteer reward",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="string")
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

class DeleteReward extends BaseReward
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.delete.volunteers'];
        $this->checkPermissions($permissions);

        $target = new GetReward($this->container);
        $data = $target->buildResource($request, $response, ['id' => $params['id']])[1];

        if (intval($data['claimed']) == 0) {
            Delete::new($this->container->db)
                ->from('VolunteerRewards')
                ->whereEquals(['PrizeID' => $params['id']])
                ->perform();
        } else {
            Update::new($this->container->db)
                ->table('VolunteerRewards')
                ->columns(['Retired' => 1,
                    'TotalInventory' => $data['claimed'],
                    'RewardGroupID' => null])
                ->whereEquals(['PrizeID' => $params['id']])
                ->perform();
        }

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        [null],
        204
        ];

    }


    /* end DeleteRewards */
}
