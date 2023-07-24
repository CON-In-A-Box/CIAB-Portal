<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Schema(
 *      schema="volunteer_reward_group",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"volunteer_reward_group"}
 *      ),
 *      @OA\Property(
 *          property="id",
 *          type="integer"
 *      ),
 *      @OA\Property(
 *          property="reward_limit",
 *          type="integer",
 *          description="Number of items allowed from the group."
 *      )
 *  )
 *
 *
 *  @OA\Schema(
 *      schema="volunteer_reward_group_list",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"volunteer_reward_group_list"}
 *      ),
 *      @OA\Property(
 *          property="data",
 *          description="Items in the group.",
 *          @OA\Schema(
 *              ref="#/components/schemas/volunteer_reward_entry_list"
 *          )
 *      )
 *  )
 *
 *   @OA\Response(
 *      response="volunteer_reward_group_not_found",
 *      description="Volunteer reward group not found in the system.",
 *      @OA\JsonContent(
 *          ref="#/components/schemas/error"
 *      )
 *   )
 **/

namespace App\Modules\volunteers\Controller\Rewards;

use Slim\Container;
use App\Controller\BaseController;
use Atlas\Query\Select;

abstract class BaseRewardGroup extends BaseController
{
    use \App\Controller\TraitScope;

    protected static $columnsToAttributes = [
        '"volunteer_reward_group"' => 'type',
        'RewardGroupID' => 'id',
        'RedeemLimit' => 'reward_limit'
    ];


    public function __construct(Container $container)
    {
        parent::__construct('volunteer_reward_group', $container);

    }


    protected function getClaimed($id)
    {
        $data = Select::new($this->container->db)
            ->columns('COUNT(DISTINCT PrizeID) AS claimed')
            ->from('HourRedemptions')
            ->whereEquals(['PrizeID', $id])
            ->fetchOne();
        return $data['claimed'];

    }


    /* END BaseRewardGroup */
}
