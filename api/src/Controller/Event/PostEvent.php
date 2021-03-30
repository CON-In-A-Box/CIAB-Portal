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
 *                      property="From",
 *                      type="string",
 *                      format="date"
 *                  ),
 *                  @OA\Property(
 *                      property="To",
 *                      type="string",
 *                      format="date"
 *                  ),
 *                  @OA\Property(
 *                      property="Name",
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

use App\Controller\InvalidParameterException;

class PostEvent extends BaseEvent
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $permissions = ['api.post.event'];
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
        if (!array_key_exists('Name', $body)) {
            throw new InvalidParameterException('Required \'Name\' parameter not present');
        }
        $name = $body['Name'];

        $target = new \App\Controller\Cycle\ListCycles($this->container);
        $newrequest = $request->withQueryParams(['includesDate' => $from]);
        $data = $target->buildResource($newrequest, $response, $args)[1];
        if (empty($data)) {
            throw new InvalidParameterException('No existing cycle contains event.');
        }
        $cycle = $data[0]['id'];
        $sql = "INSERT INTO `Events` (`EventID`, `AnnualCycleID`, `DateFrom`, `DateTo`, `EventName`) VALUES (NULL, $cycle, '$from', '$to', '$name')";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();

        $target = new \App\Controller\Event\GetEvent($this->container);
        $data = $target->buildResource($request, $response, ['id' => $this->container->db->lastInsertId()])[1];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data),
        201
        ];

    }


    /* end PostEvent */
}
