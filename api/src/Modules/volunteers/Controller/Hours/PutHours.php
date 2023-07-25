<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Put(
 *      tags={"volunteers"},
 *      path="/volunteer/hours/{id}",
 *      summary="Modifies an existing volunteer entry",
 *      @OA\Parameter(
 *          description="Id of the volunteer entry",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Parameter(
 *          parameter="force",
 *          description="Force the change. If not specified defaults 0",
 *          in="query",
 *          name="force",
 *          required=false,
 *          style="form",
 *          @OA\Schema(
 *              type="integer",
 *          )
 *      ),
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="member",
 *                      type="string",
 *                  ),
 *                  @OA\Property(
 *                      property="department",
 *                      type="string",
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
 *          response=200,
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
use Atlas\Query\Update;
use App\Controller\InvalidParameterException;

class PutHours extends BaseHours
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.put.volunteers'];
        $this->checkPermissions($permissions);
        $body = $request->getParsedBody();

        /* validate entries */
        if (array_key_exists('department', $body)) {
            $body['department'] = $this->getDepartment($body['department'])['id'];
        }
        if (array_key_exists('member', $body)) {
            $body['member'] = $this->getMember($request, $body['member'])[0]['id'];
        }
        if (array_key_exists('enterer', $body)) {
            $body['enterer'] = $this->getMember($request, $body['enterer'])[0]['id'];
        }
        if (array_key_exists('authorizer', $body)) {
            $body['authorizer'] = $this->getMember($request, $body['authorizer'])[0]['id'];
        }
        if (array_key_exists('hours', $body)) {
            if (intval($body['hours']) <= 0 || intval($body['hours']) > 24) {
                throw new InvalidParameterException('\'hours\' parameter invalid');
            }
        }
        if (array_key_exists('end', $body) &&
            strtotime($body['end']) === false) {
            throw new InvalidParameterException('\'end\' parameter invalid');
        }

        if (array_key_exists('end', $body) ||
            array_key_exists('hours', $body) ||
            array_key_exists('member', $body)) {
            $force = $request->getQueryParam('force', false);
            if (!$force) {
                $oldData = null;

                if (!array_key_exists('member', $body)) {
                    if ($oldData === null) {
                        $target = new GetHours($this->container);
                        $oldData = $target->buildResource($request, $response, ['id' => $params['id']])[1];
                    }
                    $member = $oldData['member'];
                } else {
                    $member = $body['member'];
                }

                if (!array_key_exists('end', $body)) {
                    if ($oldData === null) {
                        $target = new GetHours($this->container);
                        $oldData = $target->buildResource($request, $response, ['id' => $params['id']])[1];
                    }
                    $end = $oldData['end'];
                } else {
                    $end = $body['end'];
                }

                if (!array_key_exists('hours', $body)) {
                    if ($oldData === null) {
                        $target = new GetHours($this->container);
                        $oldData = $target->buildResource($request, $response, ['id' => $params['id']])[1];
                    }
                    $hours = $oldData['hours'];
                } else {
                    $hours = $body['hours'];
                }

                $overlap = $this->checkOverlap(
                    $request,
                    $member,
                    $end,
                    $hours
                );
                if ($overlap !== null) {
                    throw new InvalidParameterException("Entry overlaps with entry '$overlap'");
                }
            }
        }

        $insert = Update::new($this->container->db)
            ->table('VolunteerHours')
            ->columns(BaseHours::insertPayloadFromParams($body, false))
            ->WhereEquals(['HourEntryID' => $params['id']])
            ->perform();

        $target = new GetHours($this->container);
        $data = $target->buildResource($request, $response, ['id' => $params['id']])[1];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data),
        200
        ];

    }


    /* end PutHours */
}
