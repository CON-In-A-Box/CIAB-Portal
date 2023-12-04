<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Post(
 *      tags={"payments"},
 *      path="/payment/",
 *      summary="Start a new payment",
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="success",
 *                      type="string",
 *                      format="url"
 *                  ),
 *                  @OA\Property(
 *                      property="cancel",
 *                      type="string",
 *                      format="url"
 *                  ),
 *                  @OA\Property(
 *                      property="cart",
 *                      type="array",
 *                      @OA\Items(
 *                          @OA\Schema(
 *                              @OA\Property(
 *                                  property="item",
 *                                  type="string"
 *                              ),
 *                              @OA\Property(
 *                                  property="price",
 *                                  type="double"
 *                              ),
 *                              @OA\Property(
 *                                  property="quantity",
 *                                  type="integer"
 *                              )
 *                          )
 *                      )
 *                  )
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=201,
 *          description="OK",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/payment"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          description="Payment Processor Not Found.",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/error"
 *          )
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Controller\Payment;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Error\NotFoundException;

class PostPayment extends BasePayment
{


    public function baseBuildResource(Request $request, Response $response, $params): array
    {
        throw new NotFoundException();

    }


    /* end PostPayment */
}
