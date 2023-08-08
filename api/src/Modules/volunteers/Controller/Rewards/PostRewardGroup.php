<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Post(
 *      tags={"volunteers"},
 *      path="/volunteer/reward_group",
 *      summary="Adds a new volunteer reward group",
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="reward_limit",
 *                      type="integer",
 *                      description="Number of items allowed from the group."
 *                  ),
 *                  @OA\Property(
 *                      property="name",
 *                      type="string",
 *                      description="Optional name of the group."
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
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Modules\volunteers\Controller\Rewards;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Insert;
use App\Controller\InvalidParameterException;

class PostRewardGroup extends BaseRewardGroup
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.post.volunteers'];
        $this->checkPermissions($permissions);

        $required = ['reward_limit'];
        $body = $this->checkRequiredBody($request, $required);
        if (!is_numeric($body['reward_limit'])) {
            throw new InvalidParameterException('Required \'reward_limit\' parameter invalid');
        }

        $insert = Insert::new($this->container->db)
            ->into('RewardGroup')
            ->columns(BaseRewardGroup::insertPayloadFromParams($body, false));
        $insert->perform();
        $id = $insert->getLastInsertId();

        $target = new GetRewardGroup($this->container);
        $data = $target->buildResource($request, $response, ['id' => $id]);

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $data[1],
        201
        ];

    }


    /* end PostRewardGroup */
}
