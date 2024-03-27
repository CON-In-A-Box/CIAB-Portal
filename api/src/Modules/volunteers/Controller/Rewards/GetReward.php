<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"volunteers"},
 *      path="/volunteer/rewards/{id}",
 *      summary="Gets volunteer reward data",
 *      @OA\Parameter(
 *          description="Id of the volunteer reward.",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="string")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="reward data found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/volunteer_reward"
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/volunteer_reward_not_found"
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

class GetReward extends BaseReward
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $data = Select::new($this->container->db)
            ->columns(...BaseReward::selectMapping())
            ->from('VolunteerRewards')
            ->whereEquals(['PrizeID' => $params['id']])
            ->fetchOne();

        if (empty($data)) {
            throw new NotFoundException('Reward records not found');
        }

        $data['claimed'] = $this->getClaimed($data['id']);
        $data['inventory'] = intval($data['inventory']) - $data['claimed'];

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $data];

    }


    /* end GetReward */
}
