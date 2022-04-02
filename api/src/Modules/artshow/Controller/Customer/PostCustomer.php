<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Post(
 *      tags={"artshow"},
 *      path="/artshow/customer",
 *      summary="Adds new customer information",
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="member",
 *                      type="integer",
 *                  ),
 *                  @OA\Property(
 *                      property="event",
 *                      type="integer",
 *                  ),
 *                  @OA\Property(
 *                      property="identifer",
 *                      type="text"
 *                  )
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=201,
 *          description="OK",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/artshow_customer"
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Modules\artshow\Controller\Customer;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\PermissionDeniedException;
use App\Controller\ConflictException;
use Atlas\Query\Insert;
use Atlas\Query\Select;

class PostCustomer extends BaseCustomer
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $this->checkPermissions(["api.post.artshow.customer"]);

        $fields = array();
        $body = $this->checkRequiredBody($request, ['identifier']);

        if (!array_key_exists('event', $body)) {
            $body['event'] = $this->getEventId($request);
        }

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

        $insert = Insert::new($this->container->db)
            ->into('Artshow_Buyer')
            ->columns(BaseCustomer::insertPayloadFromParams($body));
        $insert->perform();
        $id = $insert->getLastInsertId();

        $target = new GetCustomer($this->container);
        $data = $target->buildResource($request, $response, ['id' => $id])[1];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data),
        201
        ];

    }


    /* end PostCustomer */
}
