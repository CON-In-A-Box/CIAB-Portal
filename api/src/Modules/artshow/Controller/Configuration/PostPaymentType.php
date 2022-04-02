<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Post(
 *      tags={"artshow"},
 *      path="/artshow/configuration/paymenttype",
 *      summary="Adds a new payment type",
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="payment",
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
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Modules\artshow\Controller\Configuration;

class PostPaymentType extends BasePaymentType
{

    protected static $required = ['payment'];

    protected static $resource = '\App\Modules\artshow\Controller\Configuration\GetPaymentType';

    protected static $get_id = 'payment';

    use TraitPost;

    /* end PostPaymentType */
}
