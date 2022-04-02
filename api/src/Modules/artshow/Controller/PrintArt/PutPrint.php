<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Put(
 *      tags={"artshow"},
 *      path="/artshow/print/{id}",
 *      summary="Updates a piece of print art",
 *      @OA\Parameter(
 *          description="Id of the piece of print art",
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
 *                      ref="#/components/schemas/artshow_print"
 *                  )
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Print updated",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/artshow_print"
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Modules\artshow\Controller\PrintArt;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;
use Atlas\Query\Update;
use App\Controller\InvalidParameterException;

class PutPrint extends BasePrint
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
            ->from('Artshow_PrintShopArt')
            ->where('PieceID = ', $piece)
            ->where('EventID = ', $eid)
            ->fetchOne();
        $this->checkPrintPermission($request, 'update', $data['ArtistID']);

        $fields = BasePrint::insertPayloadFromParams($body, false);

        Update::new($this->container->db)
            ->table('Artshow_PrintShopArt')
            ->columns($fields)
            ->whereEquals(['PieceID' => $piece, 'EventID' => $eid])
            ->perform();

        $target = new GetPrint($this->container);
        $newparams = ['piece' => $piece, 'event' => $eid];
        $data = $target->buildResource($request, $response, $newparams)[1];

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data),
        ];

    }


    /* end PutPrint */
}
