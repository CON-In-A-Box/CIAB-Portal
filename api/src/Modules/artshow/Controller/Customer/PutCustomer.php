<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Put(
 *      tags={"artshow"},
 *      path="/artshow/customer/{id}",
 *      summary="Updates customer information",
 *      @OA\Parameter(
 *          description="Id of the customer",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response"
 *      ),
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="member",
 *                      type="integer"
 *                  ),
 *                  @OA\Property(
 *                      property="Identifier",
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="event",
 *                      type="integer"
 *                  )
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="OK",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/artshow_customer"
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/customer_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Modules\artshow\Controller\Customer;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Update;
use App\Controller\InvalidParameterException;

class PutCustomer extends BaseCustomer
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $id = $params['id'];
        $this->checkCustomerPermission($request, 'update', $id);
        $body = $request->getParsedBody();
        if (!$body) {
            throw new InvalidParameterException("Body required");
        }

        if (!array_key_exists('event', $body)) {
            $body['event'] = $this->getEventId($request);
        }

        if (array_key_exists('identifier', $body)) {
            $good = false;
            try {
                $target = new FindCustomer($this->container);
                $data = $target->buildResource($request, $response, ['q' => $body['identifier']], ['event' => $body['event']])[1];
            } catch (\Exception $e) {
                $good = true;
            }
            if (!$good) {
                throw new ConflictException("Identifier is already an customer");
            }
        }

        $update = Update::new($this->container->db)
            ->table('Artshow_Buyer')
            ->columns(BaseCustomer::insertPayloadFromParams($body, false))
            ->whereEquals(['BuyerID' => $id])
            ->perform();

        $target = new GetCustomer($this->container);
        $data = $target->buildResource($request, $response, ['id' => $id])[1];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data)
        ];

    }


    /* end PutCustomer */
}
