<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Post(
 *      tags={"cycles"},
 *      path="/cycle",
 *      summary="Adds a new cycle",
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="date_from",
 *                      type="string",
 *                      format="date"
 *                  ),
 *                  @OA\Property(
 *                      property="date_to",
 *                      type="string",
 *                      format="date"
 *                  ),
*              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=201,
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
 *          description="Department or Member not found in the system",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/error"
 *          )
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Controller\Cycle;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Insert;

use App\Error\InvalidParameterException;

class PostCycle extends BaseCycle
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $permissions = ['api.post.cycle'];
        $this->checkPermissions($permissions);
        $required = ['date_from', 'date_to'];
        $body = $this->checkRequiredBody($request, $required);
        try {
            $from = date_format(new \DateTime($body['date_from']), 'Y-m-d');
        } catch (\Exception $e) {
            throw new InvalidParameterException('Required \'date_from\' parameter not valid');
        }
        try {
            $to = date_format(new \DateTime($body['date_to']), 'Y-m-d');
        } catch (\Exception $e) {
            throw new InvalidParameterException('Required \'date_to\' parameter not valid');
        }

        $insert = Insert::new($this->container->db);
        $insert->into('AnnualCycles')->columns(BaseCycle::insertPayloadFromParams($body));
        $insert->perform();
        $id = $insert->getLastInsertId();

        $target = new \App\Controller\Cycle\GetCycle($this->container);
        $data = $target->buildResource($request, $response, ['id' => $id])[1];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data),
        201
        ];

    }


    /* end PostCycle */
}
