<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"artshow"},
 *      path="/artshow/customer/{id}",
 *      summary="Gets an customer",
 *      @OA\Parameter(
 *          description="Id of the customer",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Customer found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/artshow_customer"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/customer_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Modules\artshow\Controller\Customer;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\IncludeResource;
use App\Controller\NotFoundException;
use Atlas\Query\Select;

class GetCustomer extends BaseCustomer
{


    public function __construct($container)
    {
        parent::__construct($container);
        $this->includes = [
        new IncludeResource('\App\Controller\Member\GetMember', 'id', 'member')
        ];

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $select = Select::new($this->container->db);
        $select->columns(...BaseCustomer::selectMapping())
            ->from('Artshow_Buyer');
        if (array_key_exists('id', $params)) {
            $id = $params['id'];
            $select->whereEquals(['BuyerID' => $id]);
        } else {
            $id = $request->getAttribute('oauth2-token')['user_id'];
            $select->whereEquals(['AccountID' => $id]);
        }
        $customer = $select->fetchOne();
        if (empty($customer)) {
            throw new NotFoundException('Customer Not Found');
        }
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $customer];

    }


    /* end GetCustomer */
}
