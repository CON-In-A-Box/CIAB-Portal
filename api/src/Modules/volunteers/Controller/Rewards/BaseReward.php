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
 *          type="string",
 *      ),
 *      @OA\Property(
 *          property="name",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="promo",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="reward_group",
 *          oneOf={
 *              @OA\Schema(
 *                  ref="#/components/schemas/volunteer_reward_group"
 *              ),
 *              @OA\Schema(
 *                  type="string",
 *                  description="Reward Group Id"
 *              )
 *          }
 *      ),
 *      @OA\Property(
 *          property="inventory",
 *          type="integer"
 *      ),
 *      @OA\Property(
 *          property="retired",
 *          type="string",
 *          nullable=true
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
use App\Controller\IncludeResource;
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
        'Value' => 'value',
        'Retired' => 'retired'
    ];


    public function __construct(Container $container)
    {
        parent::__construct('volunteer_reward', $container);

        $this->includes = [
        new IncludeResource('\App\Modules\volunteers\Controller\Rewards\GetRewardGroup', 'id', 'reward_group')
        ];

    }


    public static function install($container): void
    {

    }


    public static function permissions($database): ?array
    {
        return ['api.delete.volunteers', 'api.post.volunteers', 'api.put.volunteers'];

    }


    protected function getClaimed($id)
    {
        $data = Select::new($this->container->db)
            ->columns('COUNT(PrizeID) AS claimed')
            ->from('HourRedemptions')
            ->whereEquals(['PrizeID' => $id])
            ->groupBy('PrizeID')
            ->fetchOne();
        if ($data) {
            return intval($data['claimed']);
        }
        return 0;

    }


    /* END BaseReward */
}
