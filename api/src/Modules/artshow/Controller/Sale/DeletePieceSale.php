<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Delete(
 *      tags={"artshow"},
 *      path="/artshow/sale/art/{id}",
 *      summary="Deletes a sale",
 *      @OA\Parameter(
 *          description="Id of the sale",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Response(
 *          response=204,
 *          description="OK"
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/sale_not_found"
 *      ),
 *      security={
 *          {"ciab_auth": {}}
 *       }
 *  )
 **/

namespace App\Modules\artshow\Controller\Sale;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\NotFoundException;
use Atlas\Query\Select;
use Atlas\Query\Delete;

class DeletePieceSale extends DeleteSale
{


    public function __construct(Container $container)
    {
        parent::__construct($container, 'Artshow_Art_Sale');

    }


    /* end DeleteSale */
}
