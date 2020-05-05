<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Art;

use Slim\Http\Request;
use Slim\Http\Response;

class PostArt extends BaseArt
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $body = $request->getParsedBody();

        $fields = array();
        $prices = array();

        $sql = "SELECT PriceType FROM `Artshow_PriceType` WHERE SetPrice";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll();
        foreach ($data as $priceType) {
            $prices[$priceType['PriceType']] = 0;
        }

        foreach ($body as $key => $value) {
            $key = str_replace('_', ' ', $key);
            if (array_key_exists($key, $prices)) {
                $prices[$key] = $value;
            } else {
                $fields[$key] = \MyPDO::quote($value);
            }
        }

        if (!array_key_exists('EventID', $fields)) {
            $fields['EventID'] = $this->currentEvent();
        }
        if (array_key_exists('artist', $params)) {
            $fields['ArtistID'] = $params['artist'];
        }

        $check = $this->checkParameters($request, $response, $fields, ['ArtistID', 'EventID', 'Name', 'PieceType']);
        if ($check !== null) {
            return $check;
        }

        if (!$this->checkArtPermission($request, 'add', $fields['ArtistID'])) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }

        $sql = "SELECT MAX(PieceID) AS id FROM `Artshow_DisplayArt` WHERE EventID=".$fields['EventID'];
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll();
        if (empty($data) || $data[0]['id'] == null) {
            $fields['PieceID'] = 1;
        } else {
            $fields['PieceID'] = intval($data[0]['id']) + 1;
        }

        $sql = "INSERT INTO `Artshow_DisplayArt` (";
        $sql .= implode(", ", array_keys($fields));
        $sql .= ") VALUES (";
        $sql .= implode(", ", array_values($fields));
        $sql .= ")";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();

        foreach ($prices as $key => $price) {
            $sql = "INSERT INTO `Artshow_DisplayArtPrice` (";
            $sql .= "PieceID, EventID, PriceType, Price";
            $sql .= ") VALUES (";
            $sql .= $fields['PieceID'].", ".$fields['EventID'].", ";
            $sql .= \MyPDO::quote($key).", ".$price;
            $sql .= ")";
            $sth = $this->container->db->prepare($sql);
            $sth->execute();
        }

        $target = new GetArt($this->container);
        $newparams = ['piece' => $fields['PieceID'], 'event' => $fields['EventID']];
        return $target->buildResource($request, $response, $newparams);

    }


    /* end PostArt */
}
