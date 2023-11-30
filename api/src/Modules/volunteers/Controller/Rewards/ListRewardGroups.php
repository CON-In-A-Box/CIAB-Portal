<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"volunteers"},
 *      path="/volunteers/rewards_group",
 *      summary="Lists the volunteer reward groups",
 *      @OA\Response(
 *          response=200,
 *          description="reward data found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/volunteer_reward_group_list"
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Modules\volunteers\Controller\Rewards;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Error\NotFoundException;
use Atlas\Query\Select;
use App\Controller\BaseController;

class ListRewardGroups extends BaseRewardGroup
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $output = Select::new($this->container->db)
        ->columns(...BaseRewardGroup::selectMapping())
        ->from('RewardGroup')
        ->fetchAll();

        if (empty($output)) {
            throw new NotFoundException('Reward group not found');
        }

        return [
        \App\Controller\BaseController::LIST_TYPE,
        $output,
        array('type' => 'volunteer_reward_group_list')];

    }


    /* end ListRewardGroups */
}
