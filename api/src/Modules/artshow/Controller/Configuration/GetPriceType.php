<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Schema(
 *      schema="artshow_price_type",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"price_type"}
 *      ),
 *      @OA\Property(
 *          property="price",
 *          type="string",
 *          description="Price Name"
 *      ),
 *      @OA\Property(
 *          property="position",
 *          type="integer",
 *          description="Price positon on bid sheet"
 *      ),
 *      @OA\Property(
 *          property="artist_set",
 *          type="integer",
 *          description="Price is set by artist"
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="artshow_price_type_list",
 *      allOf = {
 *          @OA\Schema(ref="#/components/schemas/resource_list")
 *      },
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"price_type_list"}
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="List of price types",
 *          @OA\Items(
 *              ref="#/components/schemas/artshow_price_type"
 *          ),
 *      )
 *  )
 *
 *  @OA\Get(
 *      tags={"artshow"},
 *      path="/artshow/configuration/pricetype/{type}",
 *      summary="Gets an art show price type",
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
 *              ref="#/components/schemas/artshow_price_type"
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
 *      path="/artshow/configuration/pricetype",
 *      summary="List art show price types",
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Payment type found",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/artshow_price_type_list"
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

class GetPriceType extends BasePriceType
{

    use TraitGet;

    protected static $list_type = 'price_type_list';


    /* end GetPriceType */
}
