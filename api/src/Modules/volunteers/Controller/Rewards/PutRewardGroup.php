<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Put(
 *      tags={"volunteers"},
 *      path="/volunteer/reward_group/{id}",
 *      summary="Modifies an existing volunteer reward group",
 *      @OA\Parameter(
 *          description="Id of the volunteer reward group",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="string")
 *      ),
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="reward_limit",
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="name",
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
 *          response=400,
 *          ref="#/components/responses/400"
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

namespace App\Modules\volunteers\Controller\Rewards;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Update;
use App\Error\InvalidParameterException;

class PutRewardGroup extends BaseRewardGroup
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.put.volunteers'];
        $this->checkPermissions($permissions);
        $body = $request->getParsedBody();

        if (array_key_exists('reward_limit', $body) && !is_numeric($body['reward_limit'])) {
            throw new InvalidParameterException('\'reward_limit\' parameter invalid');
        }

        $insert = Update::new($this->container->db)
            ->table('RewardGroup')
            ->columns(BaseRewardGroup::insertPayloadFromParams($body, false))
            ->WhereEquals(['RewardGroupID' => $params['id']])
            ->perform();


        $target = new GetRewardGroup($this->container);
        $data = $target->buildResource($request, $response, ['id' => $params['id']]);

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $data[1],
        200
        ];

    }


    /* end PutRewardGroup */
}
