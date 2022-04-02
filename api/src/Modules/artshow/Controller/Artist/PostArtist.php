<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Post(
 *      tags={"artshow"},
 *      path="/artshow/artist",
 *      summary="Adds new artist information",
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
 *          response=201,
 *          description="OK",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/artshow_artist"
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
use App\Controller\PermissionDeniedException;
use App\Controller\ConflictException;
use Atlas\Query\Insert;
use Atlas\Query\Select;

class PostArtist extends BaseArtist
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $fields = array();
        $user = $request->getAttribute('oauth2-token')['user_id'];
        $body = $request->getParsedBody();

        if (!$body) {
            $body = ['member' => $user];
        } elseif (!array_key_exists('member', $body)) {
            $body['member'] = $user;
        } elseif ($body['member'] != $user) {
            $this->checkPermissions(["api.post.artshow.artist"]);
        }

        $good = false;
        try {
            $target = new GetAccountArtist($this->container);
            $data = $target->buildResource($request, $response, ['id' => $user])[1];
        } catch (\Exception $e) {
            $good = true;
        }
        if (!$good) {
            throw new ConflictException("Member is already an artist");
        }

        $insert = Insert::new($this->container->db)
            ->into('Artshow_Artist')
            ->columns(BaseArtist::insertPayloadFromParams($body));
        $insert->perform();
        $id = $insert->getLastInsertId();

        $target = new GetArtist($this->container);
        $data = $target->buildResource($request, $response, ['artist' => $id])[1];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data),
        201
        ];

    }


    /* end PostArtist */
}
