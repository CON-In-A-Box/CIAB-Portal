<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Put(
 *      tags={"artshow"},
 *      path="/artshow/configuration/paymenttype/{type}",
 *      summary="Updates an artshow payment type",
 *      @OA\Parameter(
 *          description="Payment type to update",
 *          in="path",
 *          name="type",
 *          required=true,
 *          @OA\Schema(type="string")
 *      ),
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="payment",
 *                      type="string",
 *                      nullable=false
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
 *          ref="#/components/responses/artshow_configuration_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Modules\artshow\Controller\Configuration;

class PutPaymentType extends BasePaymentType
{
    use TraitPut;

    /* end PutPaymentType */
}
