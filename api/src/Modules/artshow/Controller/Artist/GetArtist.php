<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"artshow"},
 *      path="/artshow/artist",
 *      summary="Gets currents member artist information",
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Artist found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/artshow_artist"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/artist_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 *
 *  @OA\Get(
 *      tags={"artshow"},
 *      path="/artshow/artist/{id}",
 *      summary="Gets an artist",
 *      @OA\Parameter(
 *          description="Id of the artist",
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
 *          description="Artist found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/artshow_artist"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/artist_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Modules\artshow\Controller\Artist;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\IncludeResource;
use App\Controller\NotFoundException;
use Atlas\Query\Select;

class GetArtist extends BaseArtist
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
        $select->columns(...BaseArtist::selectMapping())
            ->from('Artshow_Artist');
        if (array_key_exists('artist', $params)) {
            $id = $params['artist'];
            $select->whereEquals(['ArtistID' => $id]);
        } else {
            $id = $request->getAttribute('oauth2-token')['user_id'];
            $select->whereEquals(['AccountID' => $id]);
        }
        $artist = $select->fetchOne();
        if (empty($artist)) {
            throw new NotFoundException('Artist Not Found');
        }
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $artist];

    }


    /* end GetArtist */
}
