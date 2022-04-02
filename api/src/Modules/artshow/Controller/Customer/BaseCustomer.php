<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Schema(
 *      schema="artshow_customer",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"customer"}
 *      ),
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          description="Customer Id"
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
 *          property="identifier",
 *          type="text"
 *      ),
 *  )
 *
 *   @OA\Response(
 *      response="customer_not_found",
 *      description="Customer not found in the system.",
 *      @OA\JsonContent(
 *          ref="#/components/schemas/error"
 *      )
 *   )
 *
 *  @OA\Schema(
 *      schema="artshow_customer_list",
 *      allOf = {
 *          @OA\Schema(ref="#/components/schemas/resource_list")
 *      },
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"customer_list"}
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="List of customers",
 *          @OA\Items(
 *              ref="#/components/schemas/artshow_customer"
 *          ),
 *      )
 *  )
 **/

namespace App\Modules\artshow\Controller\Customer;

use Slim\Container;
use App\Controller\BaseController;
use App\Controller\PermissionDeniedException;
use App\Modules\artshow\Controller\BaseArtshow;
use Atlas\Query\Select;

abstract class BaseCustomer extends BaseArtshow
{

    protected static $columnsToAttributes = [
    '"customer"' => 'type',
    'BuyerID' => 'id',
    'AccountID' => 'member',
    'EventID' => 'event',
    'Identifier' => 'identifier'
    ];


    public function __construct(Container $container)
    {
        parent::__construct('customer', $container);

    }


    protected function checkCustomerPermission($request, $method, $CustomerID)
    {
        $logged = $request->getAttribute('oauth2-token')['user_id'];
        $result = Select::new($this->container->db)
            ->columns('AccountID')
            ->from('Artshow_Buyer')
            ->whereEquals(['BuyerID' => $CustomerID])
            ->fetchOne();
        if (empty($result)) {
            throw new PermissionDeniedException('Permission Denied');
        }
        $accountID = $result['AccountID'];

        if ($logged == $accountID) {
            return;
        }

        $this->checkPermissions(["api.$method.artshow.customer"]);

    }


    /* End BaseCustomer */
}
