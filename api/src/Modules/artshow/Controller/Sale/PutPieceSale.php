<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Put(
 *      tags={"artshow"},
 *      path="/artshow/sale/art/{id}",
 *      summary="Updates sale information",
 *      @OA\Parameter(
 *          description="Id of the sale",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="OK",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/artshow_sale"
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/sale_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Modules\artshow\Controller\Sale;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Update;
use App\Controller\InvalidParameterException;
use App\Controller\PermissionDeniedException;

class PutPieceSale extends PutSale
{


    public function __construct(Container $container)
    {
        parent::__construct($container, 'Artshow_Art_Sale', '\App\Modules\artshow\Controller\Sale\GetPieceSale');

    }


    /* end PutPieceSale */
}
