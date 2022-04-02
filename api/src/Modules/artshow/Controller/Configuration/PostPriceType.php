<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Post(
 *      tags={"artshow"},
 *      path="/artshow/configuration/pricetype",
 *      summary="Adds a new price type",
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="price",
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="artist_set",
 *                      description="Does the artist decide this",
 *                      type="integer",
 *                      enum={0, 1}
 *                  ),
 *                  @OA\Property(
 *                      property="fixed",
 *                      description="When sold for this it will be exactly this price",
 *                      type="integer",
 *                      enum={0, 1}
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

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Insert;

class PostPriceType extends BasePriceType
{

    protected static $required = ['price', 'artist_set'];

    protected static $resource = '\App\Modules\artshow\Controller\Configuration\GetPriceType';

    protected static $get_id = 'price';

    use TraitPost;


    /* end PostPriceType */
}
