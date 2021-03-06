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
 *                      property="From",
 *                      type="string",
 *                      format="date"
 *                  ),
 *                  @OA\Property(
 *                      property="To",
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

use App\Controller\InvalidParameterException;

class PostCycle extends BaseCycle
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $permissions = ['api.post.cycle'];
        $this->checkPermissions($permissions);

        $body = $request->getParsedBody();
        if (empty($body)) {
            throw new InvalidParameterException('Required body not present');
        }
        if (!array_key_exists('From', $body)) {
            throw new InvalidParameterException('Required \'From\' parameter not present');
        }
        if (!array_key_exists('To', $body)) {
            throw new InvalidParameterException('Required \'To\' parameter not present');
        }
        try {
            $from = date_format(new \DateTime($body['From']), 'Y-m-d');
        } catch (\Exception $e) {
            throw new InvalidParameterException('Required \'From\' parameter not valid');
        }
        try {
            $to = date_format(new \DateTime($body['To']), 'Y-m-d');
        } catch (\Exception $e) {
            throw new InvalidParameterException('Required \'To\' parameter not valid');
        }
        $sql = "INSERT INTO `AnnualCycles` (`AnnualCycleID`, `DateFrom`, `DateTo`) VALUES (NULL, '$from', '$to')";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();

        $target = new \App\Controller\Cycle\GetCycle($this->container);
        $data = $target->buildResource($request, $response, ['id' => $this->container->db->lastInsertId()])[1];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data),
        201
        ];

    }


    /* end PostCycle */
}
