<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Schema(
 *      schema="artshow_artist",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"artist"}
 *      ),
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          description="Artist Id"
 *      ),
 *      @OA\Property(
 *          property="member",
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
 *          property="company_name",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="company_name_on_sheet",
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="company_name_on_payment",
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="website",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="notes",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="professional",
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="inactive",
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="guest_of_honor",
 *          type="boolean"
 *      )
 *  )
 *
 *   @OA\Response(
 *      response="artist_not_found",
 *      description="Artist not found in the system.",
 *      @OA\JsonContent(
 *          ref="#/components/schemas/error"
 *      )
 *   )
 *
 *  @OA\Schema(
 *      schema="artshow_artist_list",
 *      allOf = {
 *          @OA\Schema(ref="#/components/schemas/resource_list")
 *      },
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"artist_list"}
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="List of artists",
 *          @OA\Items(
 *              ref="#/components/schemas/artshow_artist"
 *          ),
 *      )
 *  )
 **/

namespace App\Modules\artshow\Controller\Artist;

use Slim\Container;
use App\Controller\BaseController;
use App\Controller\PermissionDeniedException;
use App\Modules\artshow\Controller\BaseArtshow;
use Atlas\Query\Select;

abstract class BaseArtist extends BaseArtshow
{

    protected static $columnsToAttributes = [
    '"artist"' => 'type',
    'ArtistID' => 'id',
    'AccountID' => 'member',
    'CompanyName' => 'company_name',
    'CompanyNameOnSheet' => 'company_name_on_sheet',
    'CompanyNameOnPayment' => 'company_name_on_payment',
    'Website' => 'website',
    'Notes' => 'notes',
    'Professional' => 'professional',
    'Inactive' => 'inactive',
    'GuestOfHonor' => 'guest_of_honor'
    ];


    public function __construct(Container $container)
    {
        parent::__construct('artist', $container);

    }


    protected function checkArtistPermission($request, $method, $ArtistID)
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

        $this->checkPermissions(["api.$method.artshow.artist"]);

    }


    /* End BaseArtist */
}
