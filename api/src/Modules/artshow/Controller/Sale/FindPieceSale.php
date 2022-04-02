<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"artshow"},
 *      path="/artshow/sales/piece/{piece}",
 *      summary="Gets a sale by piece",
 *      @OA\Parameter(
 *          description="Id of the piece",
 *          in="path",
 *          name="piece",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/target_event",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Sale found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/artshow_sale"
 *          ),
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
use App\Controller\IncludeResource;
use App\Controller\NotFoundException;
use Atlas\Query\Select;

class FindPieceSale extends BaseSale
{


    public function __construct($container)
    {
        parent::__construct($container);

        $this->includes = [
        new IncludeResource('\App\Controller\Member\GetMember', 'id', 'buyer'),
        new IncludeResource('\App\Controller\Event\GetEvent', 'id', 'event')
        ];

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $eid = $this->getEventId($request);
        if (!array_key_exists('event', $params)) {
            $params['event'] = $eid;
        }

        $data = Select::new($this->container->db)
            ->columns(...BaseSale::selectMapping())
            ->from('Artshow_Art_Sale')
            ->whereEquals(['PieceID' => $params['piece'], 'EventID' => $params['event']])
            ->fetchOne();
        if (empty($data)) {
            throw new NotFoundException('Sale Not Found');
        }

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $data];

    }


    /* end FindPieceSale */
}
