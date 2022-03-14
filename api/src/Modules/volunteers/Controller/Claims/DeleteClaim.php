<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Delete(
 *      tags={"volunteers"},
 *      path="/volunteers/claims/{id}",
 *      summary="Deletes an existing volunteer claim",
 *      @OA\Parameter(
 *          description="Id of the volunteer claim",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Response(
 *          response=204,
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

namespace App\Modules\volunteers\Controller\Claims;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Delete;

class DeleteClaim extends BaseClaims
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.delete.volunteers'];
        $this->checkPermissions($permissions);

        Delete::new($this->container->db)
            ->from('HourRedemptions')
            ->WhereEquals(['ClaimID' => $params['id']])
            ->perform();

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        [null],
        204
        ];

    }


    /* end DeleteClaim */
}
