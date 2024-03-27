<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Delete(
 *      tags={"events"},
 *      path="/event/{id}",
 *      summary="Deletes an event",
 *      @OA\Parameter(
 *          description="Id of the event",
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
 *          response=400,
 *          ref="#/components/responses/400"
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/event_not_found"
 *      ),
 *      security={
 *          {"ciab_auth": {}}
 *       }
 *  )
 **/

namespace App\Controller\Event;

use Slim\Http\Request;
use Slim\Http\Response;

class DeleteEvent extends BaseEvent
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.delete.event'];
        $this->checkPermissions($permissions);
        $this->container->EventService->deleteById($params['id']);

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        [null],
        204
        ];

    }


    /* end DeleteEvent */
}
