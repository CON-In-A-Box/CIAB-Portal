<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Put(
 *      tags={"volunteers"},
 *      path="/volunteer/rewards/{id}/inventory",
 *      summary="Modifies the inventory of an existing volunteer reward",
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
 *                      property="difference",
 *                      type="integer"
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
use App\Error\InvalidParameterException;

class PutRewardInventory extends BaseReward
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.put.volunteers'];
        $this->checkPermissions($permissions);
        $required = ['difference'];
        $body = $this->checkRequiredBody($request, $required);
        $target = new GetReward($this->container);
        $data = $target->buildResource($request, $response, ['id' => $params['id']])[1];
        $inventory = $data['claimed'] + $data['inventory'] + $body['difference'];
        $insert = Update::new($this->container->db)
            ->table('VolunteerRewards')
            ->columns(['TotalInventory' => $inventory])
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


    /* end PutRewardInventory */
}
