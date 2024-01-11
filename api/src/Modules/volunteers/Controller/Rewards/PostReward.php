<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Post(
 *      tags={"volunteers"},
 *      path="/volunteer/rewards",
 *      summary="Adds a new volunteer reward",
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="name",
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="promo",
 *                      type="boolean"
 *                  ),
 *                  @OA\Property(
 *                      property="reward_group",
 *                          type="string",
 *                          description="Reward Group Id"
 *                  ),
 *                  @OA\Property(
 *                      property="inventory",
 *                      type="integer"
 *                  )
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=201,
 *          description="OK"
 *      ),
 *      @OA\Response(
 *          response=400,
 *          ref="#/components/responses/400"
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
use Atlas\Query\Insert;
use App\Error\InvalidParameterException;

class PostReward extends BaseReward
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.post.volunteers'];
        $this->checkPermissions($permissions);

        $required = ['name', 'promo', 'value', 'inventory'];
        $body = $this->checkRequiredBody($request, $required);

        $body['promo'] = (int)filter_var($body['promo'], FILTER_VALIDATE_BOOLEAN);
        if (intval($body['inventory']) <= 0) {
            throw new InvalidParameterException('Required \'inventory\' parameter invalid');
        }

        if (floatval($body['value']) <= 0) {
            throw new InvalidParameterException('Required \'value\' parameter invalid');
        }

        if (array_key_exists('reward_group', $body) && intval($body['reward_group']) <= 0) {
            $body['reward_group'] = null;
        }

        $insert = Insert::new($this->container->db)
            ->into('VolunteerRewards')
            ->columns(BaseReward::insertPayloadFromParams($body, false));
        $insert->perform();
        $id = $insert->getLastInsertId();

        $target = new GetReward($this->container);
        $data = $target->buildResource($request, $response, ['id' => $id])[1];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data),
        201
        ];

    }


    /* end PostReward */
}
