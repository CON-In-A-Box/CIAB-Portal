<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"volunteers"},
 *      path="/volunteers/rewards_group/{id}",
 *      summary="Gets volunteer reward group data",
 *      @OA\Parameter(
 *          description="Id of the reward group.",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="reward data found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/volunteer_reward_group"
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/volunteer_reward_group_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Modules\volunteers\Controller\Rewards;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\NotFoundException;
use Atlas\Query\Select;
use App\Controller\BaseController;

class GetRewardGroup extends BaseRewardGroup
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $output = Select::new($this->container->db)
        ->columns(...BaseRewardGroup::selectMapping())
        ->from('RewardGroup')
        ->whereEquals(['RewardGroupID' => $params['id']])
        ->fetchOne();

        if (empty($output)) {
            throw new NotFoundException('Reward group not found');
        }

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $output];

    }


    /* end GetRewardGroup */
}
