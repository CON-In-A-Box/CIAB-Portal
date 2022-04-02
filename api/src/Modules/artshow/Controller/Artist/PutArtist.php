<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Put(
 *      tags={"artshow"},
 *      path="/artshow/artist/{id}",
 *      summary="Updates artist information",
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
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="member",
 *                      type="integer",
 *                  ),
 *                  @OA\Property(
 *                      property="company_name",
 *                      type="string",
 *                  ),
 *                  @OA\Property(
 *                      property="company_name_on_sheet",
 *                      type="boolean"
 *                  ),
 *                  @OA\Property(
 *                      property="company_name_on_payment",
 *                      type="boolean"
 *                  ),
 *                  @OA\Property(
 *                      property="website",
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="notes",
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="professional",
 *                      type="boolean"
 *                  ),
 *                  @OA\Property(
 *                      property="guest_of_honor",
 *                      type="boolean"
 *                  )
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="OK",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/artshow_artist"
 *          )
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
use Atlas\Query\Update;
use App\Controller\InvalidParameterException;

class PutArtist extends BaseArtist
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $artist = $params['artist'];
        $this->checkArtistPermission($request, 'update', $artist);
        $body = $request->getParsedBody();
        if (!$body) {
            throw new InvalidParameterException("Body required");
        }

        $update = Update::new($this->container->db)
            ->table('Artshow_Artist')
            ->columns(BaseArtist::insertPayloadFromParams($body, false))
            ->whereEquals(['ArtistID' => $artist])
            ->perform();

        $target = new GetArtist($this->container);
        $data = $target->buildResource($request, $response, ['artist' => $artist])[1];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data)
        ];

    }


    /* end PutArtist */
}
