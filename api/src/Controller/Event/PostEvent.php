<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Post(
 *      tags={"events"},
 *      path="/event",
 *      summary="Adds a new event",
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
 *          response=201,
 *          description="OK"
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Cycle not found in the system which contains event dates.",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/error"
 *          )
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Controller\Event;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Insert;

use App\Controller\InvalidParameterException;

class PostEvent extends BaseEvent
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $permissions = ['api.post.event'];
        $this->checkPermissions($permissions);

        $required = ['date_from', 'date_to', 'name'];
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

        $target = new \App\Controller\Cycle\ListCycles($this->container);
        $newrequest = $request->withQueryParams(['includesDate' => $from]);
        $data = $target->buildResource($newrequest, $response, $args)[1];
        if (empty($data)) {
            throw new InvalidParameterException('No existing cycle contains event.');
        }
        $body['cycle'] = $data[0]['id'];

        $insert = Insert::new($this->container->db);
        $insert->into('Events')->columns(BaseEvent::insertPayloadFromParams($body));
        $insert->perform();
        $id = $insert->getLastInsertId();

        $target = new \App\Controller\Event\GetEvent($this->container);
        $data = $target->buildResource($request, $response, ['id' => $id])[1];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data),
        201
        ];

    }


    /* end PostEvent */
}
