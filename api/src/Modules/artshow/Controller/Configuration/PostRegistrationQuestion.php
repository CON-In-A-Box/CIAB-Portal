<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Post(
 *      tags={"artshow"},
 *      path="/artshow/configuration/registrationquestion",
 *      summary="Adds a new artist registration question",
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="text",
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="boolean",
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

class PostRegistrationQuestion extends BaseRegistrationQuestion
{

    protected static $required = ['text', 'boolean'];

    protected static $resource = '\App\Modules\artshow\Controller\Configuration\GetRegistrationQuestion';

    protected static $get_id = null;

    use TraitPost;

    /* end PostRegistrationQuestion */
}
