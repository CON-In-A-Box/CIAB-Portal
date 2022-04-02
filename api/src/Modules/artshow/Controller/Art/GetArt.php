<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"artshow"},
 *      path="/artshow/art/piece/{id}",
 *      summary="Gets information about a piece of art",
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
 *           ref="#/components/schemas/artshow_art_list"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/art_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Modules\artshow\Controller\Art;

use Slim\Http\Request;
use Slim\Http\Response;
use \App\Controller\IncludeResource;
use App\Controller\NotFoundException;
use Atlas\Query\Select;

class GetArt extends BaseArt
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
            ->columns(...BaseArt::selectMapping())
            ->columns('Artshow_DisplayArtPrice.PriceType, Artshow_DisplayArtPrice.Price')
            ->from('Artshow_DisplayArt')
            ->join('LEFT', 'Artshow_DisplayArtPrice', 'Artshow_DisplayArtPrice.PieceID = Artshow_DisplayArt.PieceID AND Artshow_DisplayArtPrice.EventID = Artshow_DisplayArt.EventID');
        if (array_key_exists('piece', $params)) {
            $id = $params['piece'];
            $select->whereEquals(['Artshow_DisplayArt.PieceID' => $id, 'Artshow_DisplayArt.EventID' => $eid]);
        } else {
            if (array_key_exists('artist', $params)) {
                $select->whereEquals(['ArtistID' => $params['artist']]);
            }
            $select->whereEquals(['Artshow_DisplayArt.EventID' => $eid])
                ->orderBy('Artshow_DisplayArt.PieceID ASC');
            $isArray = true;
        }

        $result = $select->fetchAll();
        if (empty($result)) {
            throw new NotFoundException('Art Not Found');
        }
        $output = array();
        foreach ($result as $art) {
            if (array_key_exists($art['id'], $output)) {
                $output[$art['id']][$art['PriceType']] = $art['Price'];
            } else {
                $output[$art['id']] = $art;
                $output[$art['id']][$art['PriceType']] = $art['Price'];
                unset($output[$art['id']]['PriceType']);
                unset($output[$art['id']]['Price']);
            }
        }

        $output = array_values($output);
        if ($isArray || count($output) > 1) {
            return [
            \App\Controller\BaseController::LIST_TYPE,
            $output,
            array('type' => 'art_list')];
        }
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $output[0]];

    }


    /* end GetArt */
}
