<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Schema(
 *      schema="artshow_payment_type",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"payment_type"}
 *      ),
 *      @OA\Property(
 *          property="payment",
 *          type="string",
 *          description="Payment Type"
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="artshow_payment_type_list",
 *      allOf = {
 *          @OA\Schema(ref="#/components/schemas/resource_list")
 *      },
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"payment_type_list"}
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="List of payment types",
 *          @OA\Items(
 *              ref="#/components/schemas/artshow_payment_type"
 *          ),
 *      )
 *  )
 *
 *  @OA\Get(
 *      tags={"artshow"},
 *      path="/artshow/configuration/paymenttype/{type}",
 *      summary="Gets an art show payment type",
 *      @OA\Parameter(
 *          description="Type to get",
 *          in="path",
 *          name="type",
 *          required=true,
 *          @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Payment type found",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/artshow_payment_type"
 *          ),
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
 *
 *  @OA\Get(
 *      tags={"artshow"},
 *      path="/artshow/configuration/paymenttype",
 *      summary="List art show payment types",
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Payment type found",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/artshow_payment_type_list"
 *          ),
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

class GetPaymentType extends BasePaymentType
{

    use TraitGet;

    protected static $list_type = 'payment_type_list';


    /* end GetPaymentType */
}
