<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Post(
 *      tags={"volunteers"},
 *      path="/volunteer/claims",
 *      summary="Adds a new volunteer claim",
 *      @OA\Parameter(
 *          ref="#/components/parameters/event"
 *      ),
 *      @OA\Parameter(
 *      parameter="force",
 *          description="Force the reward addition. If not specified defaults false",
 *          in="query",
 *          name="force",
 *          required=false,
 *          style="form",
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="member",
 *                      description="Id or email of the member",
 *                      nullable=false,
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="reward",
 *                      description="Id of the reward",
 *                      nullable=false,
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="event",
 *                      description="Id of the event; current event if absent",
 *                      nullable=true,
 *                      type="string"
 *                  )
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=201,
 *          description="OK"
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="User not found in the system.",
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
use Atlas\Query\Insert;
use App\Error\InvalidParameterException;

class PostClaim extends BaseClaims
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.post.volunteers'];
        $this->checkPermissions($permissions);

        $required = ['member', 'reward'];
        $body = $this->checkRequiredBody($request, $required);
        $body['event'] = $this->getEventId($request);

        /* validate entries */
        $body['member'] = $this->getMember($request, $body['member'])[0]['id'];
        $reward = $this->getReward($request, $response, $body['reward']);
        $body['reward'] = $reward['id'];

        $force = $request->getQueryParam('force', false);
        if (!$force) {
            if ($reward['inventory'] <= 0) {
                throw new InvalidParameterException("No reward in inventory.");
            }

            $data = $this->getMemberReward($request, $response, $body['member']);
            if ($data) {
                $spent = $data['spent_hours'];
            } else {
                $spent = 0;
            }

            $data = $this->getMemberHours($request, $response, $body['member']);
            if ($data) {
                $have = $data['total_hours'];
            } else {
                $have = 0;
            }

            if (($reward['promo'] && $reward['value'] > $have) ||
               (!$reward['promo'] && $spent + $reward['value'] > $have)) {
                throw new InvalidParameterException("Member does not have enough hours");
            }

            $this->checkRewardGroupLimit($request, $response, $reward, $body['member']);
        }

        $insert = Insert::new($this->container->db)
            ->into('HourRedemptions')
            ->columns(BaseClaims::insertPayloadFromParams($body));
        $insert->perform();
        $id = $insert->getLastInsertId();

        $target = new GetClaim($this->container);
        $data = $target->buildResource($request, $response, ['id' => $id])[1];
        $short = $request->getQueryParam('short_response', false);
        if (!boolval($short)) {
            $target->processIncludes($request, $response, $params, $data);
        }
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data),
        201
        ];

    }


    /* end PostClaim */
}
