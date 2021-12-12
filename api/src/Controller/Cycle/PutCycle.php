<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Put(
 *      tags={"cycles"},
 *      path="/cycle/{id}",
 *      summary="Updates a cycle",
 *      @OA\Parameter(
 *          description="Id of the cycle",
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
 *                      property="date_from",
 *                      type="string",
 *                      format="date",
 *                      nullable=true
 *                  ),
 *                  @OA\Property(
 *                      property="date_to",
 *                      type="string",
 *                      format="date",
 *                      nullable=true
 *                  ),
*              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="OK",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/cycle"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/cycle_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Controller\Cycle;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\InvalidParameterException;
use Atlas\Query\Update;

class PutCycle extends BaseCycle
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $this->getCycle($request, $response, $params);
        $permissions = ['api.put.cycle'];
        $this->checkPermissions($permissions);

        $body = $request->getParsedBody();
        if (empty($body)) {
            throw new InvalidParameterException('Required body not present');
        }

        $update = Update::new($this->container->db);
        $update->table('AnnualCycles')->columns(BaseCycle::insertPayloadFromParams($body, false));
        $update->whereEquals(['AnnualCycleID' => $params['id']]);
        $result = $update->perform();

        return [null];

    }


    /* end PutCycle */
}
