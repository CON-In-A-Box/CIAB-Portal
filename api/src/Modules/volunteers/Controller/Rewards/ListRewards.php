<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"volunteers"},
 *      path="/volunteers/rewards",
 *      summary="List volunteer reward data",
 *      @OA\Parameter(
 *          description="Show sold out items, default if false",
 *          in="query",
 *          name="sold_out",
 *          required=false,
 *          @OA\Schema(type="boolean")
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response"
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/max_results"
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/page_token"
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="reward data found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/volunteer_reward_entry_list"
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
use Atlas\Query\Select;
use App\Controller\BaseController;

class ListRewards extends BaseReward
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $soldout = $request->getQueryParam('sold_out', '0');
        $soldout = intval($soldout);

        $data = Select::new($this->container->db)
            ->columns(...BaseReward::selectMapping())
            ->from('VolunteerRewards')
            ->orderBy('`PrizeID` ASC')
            ->orderBy('`RewardGroupID` ASC')
            ->fetchAll();

        foreach ($data as $index => $entry) {
            $data[$index]['claimed'] = $this->getClaimed($entry['id']);
            if (!$soldout) {
                if ($data[$index]['inventory'] == 0 || $data[$index]['claimed'] >= $data[$index]['inventory']) {
                    unset($data[$index]);
                }
            }
        }

        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        ['type' => 'volunteer_reward_entry_list']];

    }


    /* end ListReward */
}
