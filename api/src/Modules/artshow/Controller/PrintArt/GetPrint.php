<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"artshow"},
 *      path="/artshow/print/{id}",
 *      summary="Gets information about a piece of print art",
 *      @OA\Parameter(
 *          description="Id of the piece.",
 *          in="path",
 *          name="id",
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
 *          description="Art found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/artshow_print_list"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/print_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Modules\artshow\Controller\PrintArt;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\IncludeResource;
use App\Controller\NotFoundException;
use Atlas\Query\Select;

class GetPrint extends BasePrint
{


    public function __construct($container)
    {
        parent::__construct($container);
        $this->includes = [
        new IncludeResource('\App\Modules\artshow\Controller\Artist\GetArtist', 'artist', 'artist'),
        new IncludeResource('\App\Controller\Event\GetEvent', 'id', 'event')
        ];

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $isArray = false;
        $eid = $this->getEventId($request);

        $select = Select::new($this->container->db)
            ->columns(...BasePrint::selectMapping());
        if (array_key_exists('piece', $params)) {
            $select->where('PieceID = ', $params['piece']);
        } else {
            if (array_key_exists('artist', $params)) {
                $select->where('ArtistID = ', $params['artist']);
            }
            $isArray = true;
        }
        $result = $select->where('EventID = ', $eid)
            ->orderBy('PieceID ASC')
            ->from('Artshow_PrintShopArt')
            ->fetchAll();

        if (empty($result)) {
            throw new NotFoundException('Print Art Not Found');
        }
        foreach ($result as $index => $data) {
            $sales = Select::new($this->container->db)
                ->columns("COUNT(SaleID) AS sold")
                ->from('Artshow_Print_Sale')
                ->whereEquals(['PieceID' => $data['id'], 'EventID' => $eid])
                ->fetchOne();
            $result[$index]['sold'] = $sales['sold'];
        }
        if ($isArray || count($result) > 1) {
            return [
            \App\Controller\BaseController::LIST_TYPE,
            $result,
            array('type' => 'print_list')];
        }
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $result[0]];

    }


    /* end GetPrint */
}
