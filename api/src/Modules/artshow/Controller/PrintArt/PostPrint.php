<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\PrintArt;

use Slim\Http\Request;
use Slim\Http\Response;

class PostPrint extends BasePrint
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $body = $request->getParsedBody();

        $fields = array();

        foreach ($body as $key => $value) {
            $fields[$key] = \MyPDO::quote($value);
        }


        if (!array_key_exists('EventID', $fields)) {
            $fields['EventID'] = $this->currentEvent();
        }
        if (array_key_exists('artist', $params)) {
            $fields['ArtistID'] = $params['artist'];
        }

        $check = $this->checkParameters($request, $response, $fields, ['ArtistID', 'EventID', 'Name', 'PieceType', 'Quantity', 'Price']);
        if ($check !== null) {
            return $check;
        }

        if (!$this->checkPermission($request, 'add', $fields['ArtistID'])) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }

        $sql = "SELECT MAX(PieceID) AS id FROM `Artshow_PrintShopArt` WHERE EventID=".$fields['EventID'];
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll();
        if (empty($data) || $data[0]['id'] == null) {
            $fields['PieceID'] = 1;
        } else {
            $fields['PieceID'] = intval($data[0]['id']) + 1;
        }

        $sql = "INSERT INTO `Artshow_PrintShopArt` (";
        $sql .= implode(", ", array_keys($fields));
        $sql .= ") VALUES (";
        $sql .= implode(", ", array_values($fields));
        $sql .= ")";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();

        $target = new GetPrint($this->container);
        $newparams = ['piece' => $fields['PieceID'], 'event' => $fields['EventID']];
        return $target->buildResource($request, $response, $newparams);

    }


    /* end PostPrint */
}
