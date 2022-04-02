<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Put(
 *      tags={"artshow"},
 *      path="/artshow/configuration/pricetype/{type}",
 *      summary="Updates an artshow price type",
 *      @OA\Parameter(
 *          description="Price Type to update",
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
 *                      property="price",
 *                      type="string",
 *                      description="Price Name"
 *                  ),
 *                  @OA\Property(
 *                      property="position",
 *                      type="integer",
 *                      description="Price positon on bid sheet"
 *                  ),
 *                  @OA\Property(
 *                      property="artist_set",
 *                      type="integer",
 *                      description="Price is set by artist"
 *                  ),
 *                  @OA\Property(
 *                      property="fixed",
 *                      description="When sold for this it will be exactly this price",
 *                      type="integer",
 *                      enum={0, 1}
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

class PutPriceType extends BasePriceType
{
    use TraitPut;

    /* end PutPriceType */
}
