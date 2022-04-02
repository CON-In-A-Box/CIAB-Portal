<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Delete(
 *      tags={"artshow"},
 *      path="/artshow/art/{piece}",
 *      summary="Deletes an piece of hung art",
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
 *      @OA\Response(
 *          response=204,
 *          description="OK"
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/art_not_found"
 *      ),
 *      security={
 *          {"ciab_auth": {}}
 *       }
 *  )
 **/

namespace App\Modules\artshow\Controller\Art;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\NotFoundException;
use Atlas\Query\Select;
use Atlas\Query\Delete;

class DeleteArt extends BaseArt
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $eid = $this->getEventId($request);
        $result = Select::new($this->container->db)
            ->columns('*')
            ->from('Artshow_DisplayArt')
            ->whereEquals(['PieceID' => $params['piece'], 'EventID' => $eid])
            ->fetchAll();
        if (empty($result)) {
            throw new NotFoundException('Artshow Piece Not Found');
        }
        $target = $result[0];
        $this->checkArtPermission($request, $response, 'delete', $target['ArtistID']);

        Delete::new($this->container->db)
            ->from('Artshow_DisplayArtPrice')
            ->whereEquals(['PieceID' => $target['PieceID'], 'EventID' => $eid])
            ->perform();
        Delete::new($this->container->db)
            ->from('Artshow_DisplayArt')
            ->whereEquals(['PieceID' => $target['PieceID'], 'EventID' => $eid])
            ->perform();

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        [null],
        204
        ];

    }


    /* end DeleteArt */
}
