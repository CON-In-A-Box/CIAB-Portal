<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Schema(
 *      schema="artshow_return_method_type",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"return_method_type"}
 *      ),
 *      @OA\Property(
 *          property="method",
 *          type="string",
 *          description="Return method"
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="artshow_return_method_type_list",
 *      allOf = {
 *          @OA\Schema(ref="#/components/schemas/resource_list")
 *      },
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"return_method_list"}
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="List of return_method types",
 *          @OA\Items(
 *              ref="#/components/schemas/artshow_return_method_type"
 *          ),
 *      )
 *  )
 *
 *  @OA\Get(
 *      tags={"artshow"},
 *      path="/artshow/configuration/returnmethod/{type}",
 *      summary="Gets an art show return method",
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
 *              ref="#/components/schemas/artshow_return_method_type"
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
 *      path="/artshow/configuration/returnmethod",
 *      summary="List art show return methods",
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Payment type found",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/artshow_return_method_type_list"
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

class GetReturnMethod extends BaseReturnMethod
{

    use TraitGet;

    protected static $list_type = 'return_method_list';


    /* end GetReturnMethod */
}
