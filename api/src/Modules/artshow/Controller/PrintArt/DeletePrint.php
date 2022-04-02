<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Delete(
 *      tags={"artshow"},
 *      path="/artshow/print/{piece}",
 *      summary="Deletes an piece of print art",
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
 *          ref="#/components/responses/print_not_found"
 *      ),
 *      security={
 *          {"ciab_auth": {}}
 *       }
 *  )
 **/

namespace App\Modules\artshow\Controller\PrintArt;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;
use Atlas\Query\Delete;

class DeletePrint extends BasePrint
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $eid = $this->getEventId($request);
        $target = Select::new($this->container->db)
            ->columns('*')
            ->from('Artshow_PrintShopArt')
            ->where('PieceID = ', $params['piece'])
            ->where('EventID = ', $eid)
            ->fetchOne();
        if (empty($target)) {
            throw new NotFoundException('Artshow Print Shop Piece Not Found');
        }

        $this->checkPrintPermission($request, 'delete', $target['ArtistID']);

        Delete::new($this->container->db)
            ->from('Artshow_PrintShopArt')
            ->where('PieceID = ', $target['PieceID'])
            ->where('EventID = ', $eid)
            ->perform();

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        [null],
        204
        ];

    }


    /* end DeletePrint */
}
