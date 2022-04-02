<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Schema(
 *      schema="artshow_art_price",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"art_price"}
 *      ),
 *      @OA\Property(
 *          property="price_type",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="amount",
 *          type="integer"
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="artshow_art",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"art"}
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
 *          property="medium",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="art_type",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="edition",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="not_for_sale",
 *          type="boolean"
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
 *          property="in_auction",
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
 *      ),
 *      @OA\Property(
 *          property="prices",
 *          type="array",
 *          @OA\Items(
 *              ref="#/components/schemas/artshow_art_price"
 *          ),
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="artshow_art_list",
 *      allOf = {
 *          @OA\Schema(ref="#/components/schemas/resource_list")
 *      },
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"art_list"}
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="List of art",
 *          @OA\Items(
 *              ref="#/components/schemas/artshow_art"
 *          ),
 *      )
 *  )
 *
 *   @OA\Response(
 *      response="art_not_found",
 *      description="Art not found in the system.",
 *      @OA\JsonContent(
 *          ref="#/components/schemas/error"
 *      )
 *   )
 **/

namespace App\Modules\artshow\Controller\Art;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Modules\artshow\Controller\BaseArtshow;
use App\Controller\PermissionDeniedException;
use Atlas\Query\Select;

abstract class BaseArt extends BaseArtshow
{

    protected static $columnsToAttributes = [
    '"art"' => 'type',
    'Artshow_DisplayArt.PieceID' => 'id',
    'Artshow_DisplayArt.EventID' => 'event',
    'ArtistID' => 'artist',
    'Name' => 'name',
    'Medium' => 'medium',
    'PieceType' => 'art_type',
    'Edition' => 'edition',
    'NFS' => 'not_for_sale',
    'Charity' => 'charity',
    'inAuction' => 'in_auction',
    'NonTax' => 'non_tax',
    'Notes' => 'notes',
    'Location' => 'location',
    'TagPrintCount' => 'tag_print_count',
    'Status' => 'status'
    ];


    public function __construct(Container $container)
    {
        parent::__construct('art', $container);

    }


    public function buildArt(Request $request, Response $response, $data)
    {
        $output = array();
        foreach ($data as $key => $value) {
            if ($key == 'PriceType') {
                continue;
            }
            if ($key == 'Price') {
                continue;
            }
            $output[$key] = $value;
        }
        $output['type'] = 'art';
        $output[$data['PriceType']] = $data['Price'];
        return $output;

    }


    protected function checkArtPermission($request, $response, $method, $ArtistID)
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
            if (!$this->onlineCheckinOpen($request, $response)) {
                throw new PermissionDeniedException('Permission Denied');
            }
            return;
        }

        $this->checkPermissions(["api.$method.artshow.art"]);

    }


    /* End BaseArt */
}
