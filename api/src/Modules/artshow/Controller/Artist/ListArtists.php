<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"artshow"},
 *      path="/artshow/artists",
 *      summary="List artist",
 *      @OA\Parameter(
 *          ref="#/components/parameters/target_event",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/max_results",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/page_token",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="OK",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/artshow_artist_list"
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Modules\artshow\Controller\Artist;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\NotFoundException;
use App\Controller\IncludeResource;
use Atlas\Query\Select;

class ListArtists extends BaseArtist
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
        $event = $this->getEventId($request);
        $select = Select::new($this->container->db)
            ->columns(...BaseArtist::selectMapping())
            ->from('Artshow_Artist');
        $result = $select->fetchAll();
        if (empty($result)) {
            throw new NotFoundException('Artists Not Found');
        }
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $result,
        array('type' => 'artist_list')];

    }


    /* end ListArtists */
}
