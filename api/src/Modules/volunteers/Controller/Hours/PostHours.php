<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Post(
 *      tags={"volunteers"},
 *      path="/volunteer/hours",
 *      summary="Adds a new volunteer entry",
 *      @OA\Parameter(
 *          ref="#/components/parameters/event"
 *      ),
 *      @OA\Parameter(
 *      parameter="force",
 *          description="Force the hour addition. If not specified defaults false",
 *          in="query",
 *          name="force",
 *          required=false,
 *          style="form",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="member",
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="department",
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="hours",
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="authorizer",
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="end",
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="enterer",
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="modifier",
 *                      type="string"
 *                  )
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
 *          ref="#/components/responses/400"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          description="Argument invalid.",
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
use Atlas\Query\Insert;
use App\Error\InvalidParameterException;

class PostHours extends BaseHours
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.post.volunteers'];
        $this->checkPermissions($permissions);

        $required = ['department', 'member', 'enterer', 'authorizer',
                     'hours', 'end' ];
        $body = $this->checkRequiredBody($request, $required);
        $body['event'] = $this->getEventId($request);
        if (!array_key_exists('modifier', $body)) {
            $body['modifier'] = 1.0;
        }

        /* validate entries */
        $body['department'] = $this->getDepartment(strval($body['department']))['id'];
        $body['member'] = $this->getMember($request, strval($body['member']))[0]['id'];
        $body['enterer'] = $this->getMember($request, strval($body['enterer']))[0]['id'];
        $body['authorizer'] = $this->getMember($request, strval($body['authorizer']))[0]['id'];
        if (intval($body['hours']) <= 0 || intval($body['hours']) > 24) {
            throw new InvalidParameterException('Required \'hours\' parameter invalid');
        }
        if (strtotime(strval($body['end'])) === false) {
            throw new InvalidParameterException('Required \'end\' parameter invalid');
        }

        $force = $request->getQueryParam('force', false);
        if (!$force) {
            $overlap = $this->checkOverlap(
                $request,
                $body['member'],
                $body['end'],
                $body['hours'],
                $body['event']
            );
            if ($overlap !== null) {
                throw new InvalidParameterException("Entry overlaps with entry '$overlap'");
            }
        }

        $insert = Insert::new($this->container->db)
            ->into('VolunteerHours')
            ->columns(BaseHours::insertPayloadFromParams($body));
        $insert->perform();
        $id = $insert->getLastInsertId();

        $target = new GetHours($this->container);
        $data = $target->buildResource($request, $response, ['id' => $id])[1];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data),
        201
        ];

    }


    /* end PostHours */
}
