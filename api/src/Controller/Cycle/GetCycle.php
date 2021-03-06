<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Get(
 *      tags={"cycles"},
 *      path="/cycle/{id}",
 *      summary="Gets an cycle",
 *      @OA\Parameter(
 *          description="Id of the cycle",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Cycle found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/cycle"
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
use App\Controller\NotFoundException;

class GetCycle extends BaseCycle
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $sth = $this->container->db->prepare("SELECT * FROM `AnnualCycles` WHERE `AnnualCycleID` = ".$args['id']);
        $sth->execute();
        $cycles = $sth->fetchAll();
        if (empty($cycles)) {
            throw new NotFoundException('Cycle Not Found');
        }
        $this->id = $args['id'];
        $entry = $cycles[0];
        $entry['id'] = $this->id;
        unset($entry['AnnualCycleID']);
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $entry
        ];

    }


    /* end GetCycle */
}
