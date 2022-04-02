<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Post(
 *      tags={"artshow"},
 *      path="/artshow/configuration/piecetype",
 *      summary="Adds a new piece type",
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="piece",
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

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Insert;

class PostPieceType extends BasePieceType
{

    protected static $required = ['piece'];

    protected static $resource = '\App\Modules\artshow\Controller\Configuration\GetPieceType';

    protected static $get_id = 'piece';

    use TraitPost;

    /* end PostPieceType */
}
