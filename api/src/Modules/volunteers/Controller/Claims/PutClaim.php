<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Put(
 *      tags={"volunteers"},
 *      path="/volunteer/claims/{id}",
 *      summary="Modifies an existing volunteer claim",
 *      @OA\Parameter(
 *          description="Id of the volunteer claim",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *      parameter="force",
 *          description="Force the reward addition. If not specified defaults false",
 *          in="query",
 *          name="force",
 *          required=false,
 *          style="form",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="member",
 *                      description="Id or email of the member",
 *                      nullable=true,
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="reward",
 *                      description="Id of the reward",
 *                      nullable=true,
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="event",
 *                      description="Id of the event",
 *                      nullable=true,
 *                      type="string"
 *                  )
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
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

namespace App\Modules\volunteers\Controller\Claims;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Update;
use App\Controller\IncludeResource;

class PutClaim extends BaseClaims
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.put.volunteers'];
        $this->checkPermissions($permissions);
        $body = $request->getParsedBody();

        $target = new GetClaim($this->container);
        $current = $target->buildResource($request, $response, ['id' => $params['id']])[1];

        /* validate entries */
        if (array_key_exists('member', $body)) {
            $member = $this->getMember($request, $body['member'])[0]['id'];
            $body['member'] = $member;
            $same_member = false;
        } else {
            $member = $current['member'];
            $same_member = true;
        }

        $old_reward = $this->getReward($request, $response, $current['reward']);
        if (array_key_exists('reward', $body)) {
            $reward = $this->getReward($request, $response, $body['reward']);
            $body['reward'] = $reward['id'];
        } else {
            $reward = $old_reward;
        }

        /* Nothing Changed? */
        if ($same_member && $old_reward['id'] == $reward['id']) {
            IncludeResource::processIncludes($target->includes, $request, $response, $target->container, $params, $current);
            return [
            \App\Controller\BaseController::RESOURCE_TYPE,
            $target->arrayResponse($request, $response, $current),
            200
            ];
        }

        $force = $request->getQueryParam('force', false);
        if (!boolval($force)) {
            $data = $this->getMemberReward($request, $response, $member);
            if ($data['reward_count'] <= 0) {
                throw new InvalidParameterException("No reward in inventory.");
            }

            $spent = $data['spent_hours'];

            $data = $this->getMemberHours($request, $response, $member);
            $have = $data['total_hours'];
            if ($same_member && !$old_reward['promo']) {
                $have += $old_reward['value'];
            }

            if (($reward['promo'] && $reward['value'] > $have) ||
               (!$reward['promo'] && $spent + $reward['value'] > $have)) {
                throw new InvalidParameterException("Member does not have enough hours");
            }

            $this->checkRewardGroupLimit($request, $response, $reward, $member, $old_reward);
        }

        $insert = Update::new($this->container->db)
            ->table('HourRedemptions')
            ->columns(BaseClaims::insertPayloadFromParams($body, false))
            ->WhereEquals(['ClaimID' => $params['id']])
            ->perform();

        $target = new GetClaim($this->container);
        $data = $target->buildResource($request, $response, ['id' => $params['id']])[1];
        IncludeResource::processIncludes($target->includes, $request, $response, $target->container, $params, $data);
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data),
        200
        ];

    }


    /* end PutClaim */
}
