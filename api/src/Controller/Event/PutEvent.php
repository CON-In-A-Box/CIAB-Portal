<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Put(
 *      tags={"events"},
 *      path="/event/{id}",
 *      summary="Modifies an existing event.",
 *      @OA\Parameter(
 *          description="Id of the event",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="string")
 *      ),
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
 *                  @OA\Property(
 *                      property="name",
 *                      type="string"
 *                  ),
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
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
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Controller\Event;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Update;

class PutEvent extends BaseEvent
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.put.event'];
        $this->checkPermissions($permissions);

        $event = $this->getEvent($params['id']);
        $body = $this->buildPutPostBody($request, $response, $params, [], $event);

        Update::new($this->container->db)
            ->table('Events')
            ->columns(BaseEvent::insertPayloadFromParams($body, false))
            ->whereEquals(['EventID' => $params['id']])
            ->perform();

        $target = new \App\Controller\Event\GetEvent($this->container);
        $data = $target->buildResource($request, $response, ['id' => $params['id']])[1];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data),
        200
        ];

    }


    /* end PutEvent */
}
