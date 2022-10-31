<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Schema(
 *      schema="volunteer_reward",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"volunteer_reward"}
 *      ),
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *      ),
 *      @OA\Property(
 *          property="name",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="promo",
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="reward_group",
 *          oneOf={
 *              @OA\Schema(
 *                  ref="#/components/schemas/volunteer_reward_group"
 *              ),
 *              @OA\Schema(
 *                  type="integer",
 *                  description="Reward Group Id"
 *              )
 *          }
 *      ),
 *      @OA\Property(
 *          property="inventory",
 *          type="integer"
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="volunteer_reward_entry_list",
 *      allOf = {
 *          @OA\Schema(ref="#/components/schemas/resource_list")
 *      },
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"volunteer_reward_entry_list"}
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="List of volunteer rewards",
 *          @OA\Items(
 *              ref="#/components/schemas/volunteer_reward"
 *          )
 *      )
 *  )
 *
 *   @OA\Response(
 *      response="volunteer_reward_not_found",
 *      description="Volunteer reward not found in the system.",
 *      @OA\JsonContent(
 *          ref="#/components/schemas/error"
 *      )
 *   )
 **/

namespace App\Modules\volunteers\Controller\Rewards;

use Slim\Container;
use App\Controller\BaseController;
use Atlas\Query\Select;

abstract class BaseReward extends BaseController
{
    use \App\Controller\TraitScope;

    protected static $columnsToAttributes = [
        '"volunteer_reward"' => 'type',
        'PrizeID' => 'id',
        'Name' => 'name',
        'Promo' => 'promo',
        'RewardGroupID' => 'reward_group',
        'TotalInventory' => 'inventory',
        'Value' => 'value'
    ];


    public function __construct(Container $container)
    {
        parent::__construct('volunteer_reward', $container);

    }


    protected function getClaimed($id)
    {
        $data = Select::new($this->container->db)
            ->columns('COUNT(DISTINCT PrizeID) AS claimed')
            ->from('HourRedemptions')
            ->whereEquals(['PrizeID', $id])
            ->fetchOne();
        return intval($data['claimed']);

    }


    /* END BaseReward */
}
