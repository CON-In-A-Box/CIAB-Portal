<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Put(
 *      tags={"volunteers"},
 *      path="/volunteer/rewards/{id}",
 *      summary="Modifies an existing volunteer reward",
 *      @OA\Parameter(
 *          description="Id of the volunteer reward",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
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
 *                          type="integer",
 *                          description="Reward Group Id"
 *                  ),
 *                  @OA\Property(
 *                      property="inventory",
 *                      type="integer"
 *                  ),
 *                  @OA\Property(
 *                      property="value",
 *                      type="float"
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

namespace App\Modules\volunteers\Controller\Rewards;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Update;
use App\Controller\InvalidParameterException;

class PutReward extends BaseReward
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.put.volunteers'];
        $this->checkPermissions($permissions);
        $body = $request->getParsedBody();

        if (array_key_exists('inventory', $body)) {
            if (!is_numeric($body['inventory'])) {
                throw new InvalidParameterException('\'inventory\' parameter invalid');
            }
            $target = new GetReward($this->container);
            $data = $target->buildResource($request, $response, ['id' => $params['id']])[1];
            $body['inventory'] += $data['claimed'];
        }
        if (array_key_exists('promo', $body)) {
            $body['promo'] = (int)filter_var($body['promo'], FILTER_VALIDATE_BOOLEAN);
        }
        if (array_key_exists('value', $body) && floatval($body['value']) <= 0) {
            throw new InvalidParameterException('\'value\' parameter invalid');
        }

        $insert = Update::new($this->container->db)
            ->table('VolunteerRewards')
            ->columns(BaseReward::insertPayloadFromParams($body, false))
            ->WhereEquals(['PrizeID' => $params['id']])
            ->perform();

        $target = new GetReward($this->container);
        $data = $target->buildResource($request, $response, ['id' => $params['id']])[1];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data),
        200
        ];

    }


    /* end PutReward */
}
