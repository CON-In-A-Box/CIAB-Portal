<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"artshow"},
 *      path="/artshow/customer/find/{query}",
 *      summary="Find a customer by identifier",
 *      @OA\Parameter(
 *          description="Query string",
 *          in="path",
 *          name="query",
 *          required=true,
 *          @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          description="Allow partial matches, default is false",
 *          in="query",
 *          name="partial",
 *          required=false,
 *          @OA\Schema(type="boolean")
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/target_event",
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

class FindCustomer extends BaseCustomer
{


    public function __construct($container)
    {
        parent::__construct($container);

        $this->includes = [
        new IncludeResource('\App\Controller\Member\GetMember', 'id', 'member'),
        new IncludeResource('\App\Controller\Event\GetEvent', 'id', 'event')
        ];

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $eid = $this->getEventId($request);
        if (!array_key_exists('event', $params)) {
            $params['event'] = $eid;
        }

        $p = $request->getQueryParam('partial', 'false');
        $partial = filter_var($p, FILTER_VALIDATE_BOOLEAN);

        $query = Select::new($this->container->db)
            ->columns(...BaseCustomer::selectMapping())
            ->from('Artshow_Buyer')
            ->whereEquals(['EventID' => $params['event']]);
        $q = $params['q'];
        if ($partial) {
            $query->andWhere('Identifier LIKE ', "%$q%");
        } else {
            $query->andWhere('Identifier = ', $q);
        }
        $data = $query->fetchOne();
        if (empty($data)) {
            throw new NotFoundException('Customer Not Found');
        }

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $data];

    }


    /* end FindCustomer */
}
