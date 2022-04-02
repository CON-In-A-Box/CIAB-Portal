<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Schema(
 *      schema="artshow_print",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"print"}
 *      ),
 *      @OA\Property(
 *          property="id",
 *          type="integer"
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
 *          property="artist",
 *          oneOf={
 *              @OA\Schema(
 *                  ref="#/components/schemas/artshow_artist"
 *              ),
 *              @OA\Schema(
 *                  type="integer",
 *                  description="Artist Id"
 *              )
 *          }
 *      ),
 *      @OA\Property(
 *          property="name",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="art_type",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="quantity",
 *          type="integer"
 *      ),
 *      @OA\Property(
 *          property="price",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="charity",
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="non_tax",
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="notes",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="location",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="tag_print_count",
 *          type="integer"
 *      ),
 *      @OA\Property(
 *          property="status",
 *          type="string"
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="artshow_print_list",
 *      allOf = {
 *          @OA\Schema(ref="#/components/schemas/resource_list")
 *      },
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"print_list"}
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="List of prints",
 *          @OA\Items(
 *              ref="#/components/schemas/artshow_print"
 *          ),
 *      )
 *  )
 *   @OA\Response(
 *      response="print_not_found",
 *      description="Print not found in the system.",
 *      @OA\JsonContent(
 *          ref="#/components/schemas/error"
 *      )
 *   )
 *
 *  @OA\Schema(
 *      schema="BasePrint"
 *  )
 **/

namespace App\Modules\artshow\Controller\PrintArt;

use Slim\Container;
use Slim\Http\Request;
use App\Modules\artshow\Controller\BaseArtshow;
use App\Controller\PermissionDeniedException;
use Atlas\Query\Select;

abstract class BasePrint extends BaseArtshow
{

    protected static $columnsToAttributes = [
    '"print"' => 'type',
    'PieceID' => 'id',
    'EventID' => 'event',
    'ArtistID' => 'artist',
    'Name' => 'name',
    'PieceType' => 'art_type',
    'Quantity' => 'quantity',
    'Price' => 'price',
    'Charity' => 'charity',
    'NonTax' => 'non_tax',
    'Notes' => 'notes',
    'Location' => 'location',
    'TagPrintCount' => 'tag_print_count',
    'Status' => 'status'
    ];


    public function __construct(Container $container)
    {
        parent::__construct('print', $container);

    }


    protected function checkPrintPermission($request, $method, $ArtistID)
    {
        $logged = $request->getAttribute('oauth2-token')['user_id'];

        $result = Select::new($this->container->db)
            ->columns('AccountID')
            ->from('Artshow_Artist')
            ->whereEquals(['ArtistID' => $ArtistID])
            ->fetchOne();
        if (empty($result)) {
            throw new PermissionDeniedException('Permission Denied');
        }
        $accountID = $result['AccountID'];

        if ($logged == $accountID) {
            return;
        }

        if (!$this->havePermissions(["api.$method.artshow.print"])) {
            throw new PermissionDeniedException('Permission Denied');
        }

    }


    /* End BasePrint */
}
