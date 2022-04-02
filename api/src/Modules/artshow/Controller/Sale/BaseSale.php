<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Schema(
 *      schema="artshow_sale",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"sale"}
 *      ),
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          description="Sale Id"
 *      ),
 *      @OA\Property(
 *          property="display",
 *          type="boolean",
 *          description="If not display then printshop"
 *      ),
 *      @OA\Property(
 *          property="piece",
 *          oneOf={
 *              @OA\Schema(
 *                  ref="#/components/schemas/artshow_art"
 *              ),
 *              @OA\Schema(
 *                  ref="#/components/schemas/artshow_print"
 *              ),
 *              @OA\Schema(
 *                  type="integer",
 *                  description="Piece Id"
 *              )
 *          }
 *      ),
 *      @OA\Property(
 *          property="event",
 *          oneOf={
 *              @OA\Schema(
 *                  ref="#/components/schemas/event"
 *              ),
 *              @OA\Schema(
 *                  type="integer",
 *                  description="Event Id"
 *              )
 *          }
 *      ),
 *      @OA\Property(
 *          property="buyer",
 *          oneOf={
 *              @OA\Schema(
 *                  ref="#/components/schemas/member"
 *              ),
 *              @OA\Schema(
 *                  type="integer",
 *                  description="Member Id"
 *              )
 *          }
 *      ),
 *      @OA\Property(
 *          property="price_type",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="price",
 *          type="number"
 *      )
 *  )
 *
 *   @OA\Response(
 *      response="sale_not_found",
 *      description="Sale not found in the system.",
 *      @OA\JsonContent(
 *          ref="#/components/schemas/error"
 *      )
 *   )
 *
 *  @OA\Schema(
 *      schema="artshow_sale_list",
 *      allOf = {
 *          @OA\Schema(ref="#/components/schemas/resource_list")
 *      },
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"sale_list"}
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="List of sales",
 *          @OA\Items(
 *              ref="#/components/schemas/artshow_sale"
 *          ),
 *      )
 *  )
 **/

namespace App\Modules\artshow\Controller\Sale;

use Slim\Container;
use App\Controller\BaseController;
use App\Controller\PermissionDeniedException;
use App\Modules\artshow\Controller\BaseArtshow;
use Atlas\Query\Select;

abstract class BaseSale extends BaseArtshow
{

    protected static $columnsToAttributes = [
    '"sale"' => 'type',
    'SaleID' => 'id',
    'PieceID' => 'piece',
    'EventID' => 'event',
    'BuyerID' => 'buyer',
    'PriceType' => 'price_type',
    'Price' => 'price'
    ];


    public function __construct(Container $container)
    {
        parent::__construct('sale', $container);

    }


    /* End BaseSale */
}
