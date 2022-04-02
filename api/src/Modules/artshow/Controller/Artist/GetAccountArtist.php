<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"artshow"},
 *      path="/artshow/artist/member/{id}",
 *      summary="Gets an artist from member id",
 *      @OA\Parameter(
 *          description="Id of the member",
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

class GetAccountArtist extends BaseArtist
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
        $artist = $this->getMember($request, $params['id'])[0];
        $query = Select::new($this->container->db)
            ->columns(...BaseArtist::selectMapping())
            ->from('Artshow_Artist')
            ->whereEquals(['AccountID' => $artist['id']])
            ->fetchOne();
        if (empty($query)) {
            throw new NotFoundException('Artist Not Found');
        }
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $query];

    }


    /* end GetAccountArtist */
}
