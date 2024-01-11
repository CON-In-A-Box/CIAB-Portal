<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Delete(
 *      tags={"volunteers"},
 *      path="/volunteer/hours/{id}",
 *      summary="Deletes an existing volunteer entry",
 *      @OA\Parameter(
 *          description="Id of the volunteer entry",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="string")
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

namespace App\Modules\volunteers\Controller\Hours;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Delete;
use App\Error\InvalidParameterException;

class DeleteHours extends BaseHours
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.delete.volunteers'];
        $this->checkPermissions($permissions);

        Delete::new($this->container->db)
            ->from('VolunteerHours')
            ->WhereEquals(['HourEntryID' => $params['id']])
            ->perform();

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        [null],
        204
        ];

    }


    /* end DeleteHours */
}
