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
 *                      property="From",
 *                      type="string",
 *                      format="date",
 *                      nullable=true
 *                  ),
 *                  @OA\Property(
 *                      property="To",
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

class PutCycle extends BaseCycle
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $this->getCycle($params);
        $permissions = ['api.put.cycle'];
        $this->checkPermissions($permissions);

        $sql = "UPDATE `AnnualCycles` SET ";

        $body = $request->getParsedBody();
        if (empty($body)) {
            throw new InvalidParameterException('Required body not present');
        }
        $changes = array();

        if (array_key_exists('From', $body)) {
            $from = date_format(new \DateTime($body['From']), 'Y-m-d');
            $changes[] = "`DateFrom` = '$from'";
        }

        if (array_key_exists('To', $body)) {
            $to = date_format(new \DateTime($body['To']), 'Y-m-d');
            $changes[] = "`DateTo` = '$to'";
        }

        if (count($changes) > 0) {
            $sql .= implode(',', $changes);
            $sql .= " WHERE `AnnualCycleID` = '".$params['id']."';";

            print $sql;
            $sth = $this->container->db->prepare($sql);
            $sth->execute();
        }
        return [null];

    }


    /* end PutCycle */
}
