<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\PrintArt;

use Slim\Http\Request;
use Slim\Http\Response;

class PutPrint extends BasePrint
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $eid = $this->getEvent($params, 'event');
        $piece  = $params['piece'];

        $body = $request->getParsedBody();

        $sql = "SELECT `ArtistID` FROM `Artshow_PrintShopArt` ";
        $sql .= "WHERE PieceID = $piece AND EventID = $eid";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll();
        if (!$this->checkPermission($request, 'update', $data[0]['ArtistID'])) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }

        foreach ($body as $key => $value) {
            $fields[] = " `$key` = ".\MyPDO::quote($value);
        }

        $sql = "UPDATE `Artshow_PrintShopArt` SET ";
        $sql .= implode(", ", $fields);
        $sql .= "WHERE PieceID = $piece AND EventID = $eid";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();

        $target = new GetPrint($this->container);
        $newparams = ['piece' => $piece, 'event' => $eid];
        return $target->buildResource($request, $response, $newparams);

    }


    /* end PutPrint */
}
