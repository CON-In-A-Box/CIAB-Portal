<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Schema(
 *      schema="artshow_artist_event",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"artist_event"}
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
 *          property="mail_in",
 *          type="boolean",
 *          description="Is this mail in art?"
 *      ),
 *      @OA\Property(
 *          property="return_method",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="insurance_amount",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="initial_payment",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="payment_type",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="check_number",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="notes",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="return_labels",
 *          type="string"
 *      )
 *  )
 *
 **/

namespace App\Modules\artshow\Controller\Show;

use Slim\Container;
use App\Modules\artshow\Controller\BaseArtshow;
use Atlas\Query\Select;
use App\Controller\PermissionDeniedException;

abstract class BaseShow extends BaseArtshow
{

    protected static $columnsToAttributes = [
    '"artist_event"' => 'type',
    'EventID' => 'event',
    'ArtistID' => 'artist',
    'MailIn' => 'mail_in',
    'ReturnMethod' => 'return_method',
    'InsuranceAmount' => 'insurance_amount',
    'InitialPayment' => 'initial_payment',
    'PaymentType' => 'payment_type',
    'CheckNumber' => 'check_number',
    'Notes' => 'notes',
    'ReturnLabels' => 'return_labels'
    ];


    public function __construct(Container $container)
    {
        parent::__construct('artshow', $container);

    }


    protected function checkShowPermission($request, $method, $ArtistID)
    {
        $logged = $request->getAttribute('oauth2-token')['user_id'];

        $result = Select::new($this->container->db)
            ->columns('AccountID')
            ->from('Artshow_Artist')
            ->whereEquals(['ArtistID ' => $ArtistID])
            ->fetchOne();
        if (empty($result)) {
            throw new PermissionDeniedException('Permission Denied');
        }
        $accountID = $result['AccountID'];

        if ($logged == $accountID) {
            return;
        }

        $this->checkPermissions(["api.$method.artshow.show"]);

    }


    /* End BaseArtShow */
}
