<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Post(
 *      tags={"artshow"},
 *      path="/artshow/configuration/returnmethod",
 *      summary="Adds a new return method",
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="method",
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

class PostReturnMethod extends BaseReturnMethod
{

    protected static $required = ['method'];

    protected static $resource = '\App\Modules\artshow\Controller\Configuration\GetReturnMethod';

    protected static $get_id = 'method';

    use TraitPost;

    /* end PostReturnMethod */
}
