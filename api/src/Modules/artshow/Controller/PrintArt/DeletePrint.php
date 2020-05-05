<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\PrintArt;

use Slim\Http\Request;
use Slim\Http\Response;

class DeletePrint extends BasePrint
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $eid = $this->getEvent($params, 'event');

        $sth = $this->container->db->prepare("SELECT * FROM `Artshow_PrintShopArt` WHERE `PieceID` = ".$params['piece']." AND `EventID` = ".$eid);
        $sth->execute();
        $result = $sth->fetchAll();
        if (empty($result)) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Not Found', 'Artshow Print Shop Piece Not Found', 404)];
        }
        $target = $result[0];
        if (!$this->checkPermission($request, 'delete', $target['ArtistID'])) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }

        $sth = $this->container->db->prepare(<<<SQL
            DELETE FROM `Artshow_PrintShopArt`
            WHERE `PieceID` = '{$target['PieceID']}' AND
                  `EventID` = $eid;
SQL
        );
        $sth->execute();
        return [null];

    }


    /* end DeletePrint */
}
