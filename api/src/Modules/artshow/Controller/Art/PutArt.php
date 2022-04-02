<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Put(
 *      tags={"artshow"},
 *      path="/artshow/art/{id}",
 *      summary="Updates a piece of art",
 *      @OA\Parameter(
 *          description="Id of the piece of art",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      ref="#/components/schemas/artshow_art"
 *                  )
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Art updated",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/artshow_art"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Modules\artshow\Controller\Art;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;
use Atlas\Query\Update;
use App\Controller\InvalidParameterException;

class PutArt extends BaseArt
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $eid = $this->getEventId($request);
        $piece  = $params['piece'];

        $body = $request->getParsedBody();
        if (!$body) {
            throw new InvalidParameterException("Body required");
        }

        $data = Select::new($this->container->db)
            ->columns('ArtistID')
            ->from('Artshow_DisplayArt')
            ->whereEquals(['PieceID' => $piece, 'EventID' => $eid])
            ->fetchOne();
        $this->checkArtPermission($request, $response, 'update', $data['ArtistID']);

        $prices = array();

        $data = Select::new($this->container->db)
            ->columns('PriceType')
            ->from('Artshow_PriceType')
            ->where('SetPrice')
            ->fetchAll();
        foreach ($data as $priceType) {
            $prices[$priceType['PriceType']] = null;
        }

        foreach ($body as $key => $value) {
            $key = str_replace('_', ' ', $key);
            if (array_key_exists($key, $prices)) {
                $prices[$key] = $value;
            }
        }

        $fields = BaseArt::insertPayloadFromParams($body, false);
        $fields['EventID'] = $eid;
        $fields['PieceID'] = $piece;
        unset($fields['Artshow_DisplayArt.EventID']);
        unset($fields['Artshow_DisplayArt.PieceID']);

        Update::new($this->container->db)
            ->table('Artshow_DisplayArt')
            ->columns($fields)
            ->whereEquals(['PieceID' => $piece, 'EventID' => $eid])
            ->perform();

        foreach ($prices as $key => $price) {
            if ($price !== null) {
                Update::new($this->container->db)
                    ->table('Artshow_DisplayArtPrice')
                    ->columns(['Price' => $price])
                    ->whereEquals(['PieceID' => $piece, 'EventID' => $eid, 'PriceType' => $key])
                    ->perform();
            }
        }

        $target = new GetArt($this->container);
        $newparams = ['piece' => $piece, 'event' => $eid];
        $data = $target->buildResource($request, $response, $newparams)[1];

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data),
        ];

    }


    /* end PutArt */
}
