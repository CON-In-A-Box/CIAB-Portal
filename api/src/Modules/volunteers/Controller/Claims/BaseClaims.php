<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Schema(
 *      schema="volunteer_claim",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"volunteer_claim"}
 *      ),
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *      ),
 *      @OA\Property(
 *          property="member",
 *          description="Member badge who volunteered",
 *          oneOf={
 *              @OA\Schema(
 *                  ref="#/components/schemas/member"
 *              ),
 *              @OA\Schema(
 *                  type="integer",
 *                  description="Member Id"
 *              )
 *          }
 *      ),
 *      @OA\Property(
 *          property="event",
 *          description="Event the badge is for",
 *          oneOf={
 *              @OA\Schema(
 *                  ref="#/components/schemas/event"
 *              ),
 *              @OA\Schema(
 *                  type="integer",
 *                  description="Event Id"
 *              )
 *          }
 *      ),
 *      @OA\Property(
 *          property="reward",
 *          description="The Reward.",
 *          oneOf={
 *              @OA\Schema(
 *                  ref="#/components/schemas/volunteer_reward"
 *              ),
 *              @OA\Schema(
 *                  type="integer",
 *                  description="Reward Id"
 *              )
 *          }
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="volunteer_claim_list",
 *      allOf = {
 *          @OA\Schema(ref="#/components/schemas/resource_list")
 *      },
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"volunteer_claim_list"}
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="List of volunteer claims",
 *          @OA\Items(
 *              ref="#/components/schemas/volunteer_claim"
 *          )
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="volunteer_claim_summary",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"volunteer_claim_summary"}
 *      ),
 *      @OA\Property(
 *          property="reward_count",
 *          type="integer",
 *      ),
 *      @OA\Property(
 *          property="spent_hours",
 *          type="integer",
 *      ),
 *      @OA\Property(
 *          property="event",
 *          description="Event of the claim summary",
 *          oneOf={
 *              @OA\Schema(
 *                  ref="#/components/schemas/event"
 *              ),
 *              @OA\Schema(
 *                  type="integer",
 *                  description="Event Id"
 *              )
 *          }
 *      )
 *  )
 *
 *   @OA\Response(
 *      response="volunteer_claim_not_found",
 *      description="Volunteer claim not found in the system.",
 *      @OA\JsonContent(
 *          ref="#/components/schemas/error"
 *      )
 *   )
 **/

namespace App\Modules\volunteers\Controller\Claims;

use Slim\Container;
use App\Controller\BaseController;
use Atlas\Query\Select;
use App\Error\InvalidParameterException;

abstract class BaseClaims extends BaseController
{
    use \App\Controller\TraitScope;

    protected static $columnsToAttributes = [
        '"volunteer_claim"' => 'type',
        'ClaimID' => 'id',
        'AccountID' => 'member',
        'EventID' => 'event',
        'PrizeID' => 'reward'
    ];


    public function __construct(Container $container)
    {
        parent::__construct('volunteer_claim', $container);

    }


    public static function install($container): void
    {

    }


    public static function permissions($database): ?array
    {
        return ['api.delete.volunteers', 'api.get.volunteer.hours',
            'api.get.volunteer.hours', 'api.get.volunteer.claims',
            'api.post.volunteers', 'api.put.volunteers'];

    }


    protected function getReward($request, $response, $id)
    {
        $target = new \App\Modules\volunteers\Controller\Rewards\GetReward($this->container);
        return $target->buildResource($request, $response, ['id' => $id])[1];

    }


    protected function getMemberReward($request, $response, $id)
    {
        $target = new GetMemberClaimsSummary($this->container);
        try {
            return $target->buildResource($request, $response, ['id' => $id])[1];
        } catch (\Exception $e) {
            return null;
        }

    }


    protected function getMemberHours($request, $response, $id)
    {
        try {
            $target = new \App\Modules\volunteers\Controller\Hours\GetMemberHoursSummary($this->container);
            return $target->buildResource($request, $response, ['id' => $id])[2];
        } catch (\Exception $e) {
            return null;
        }

    }


    protected function checkRewardGroupLimit($request, $response, $reward, $member, $old_reward = null)
    {
        if ($reward['reward_group'] === null) {
            return;
        }

        if ($old_reward && $old_reward['reward_group'] == $reward['reward_group']) {
            return;
        }

        $target = new \App\Modules\volunteers\Controller\Rewards\GetRewardGroup($this->container);
        $group = $target->buildResource($request, $response, ['id' => $reward['reward_group']])[1];
        if ($group['reward_limit'] == 0) {
            return;
        }
        $data = Select::new($this->container->db)
        ->columns('COUNT(h.PrizeID) AS current')
        ->columns('g.RedeemLimit AS the_limit')
        ->from('HourRedemptions AS h')
        ->join(
            'LEFT',
            'VolunteerRewards AS d',
            'h.PrizeID = d.PrizeID'
        )
        ->join(
            'LEFT',
            'RewardGroup AS g',
            'd.RewardGroupID = g.RewardGroupID'
        )
        ->whereEquals(['h.AccountID' => $member,
            'd.RewardGroupID' => $reward['reward_group']]);
        $data = $data->fetchOne();

        if ($data['current'] && $data['current'] + 1 > $data['the_limit']) {
            throw new InvalidParameterException('Exceeds group reward limit');
        }

    }


    /* END BaseClaims */
}
