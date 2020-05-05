<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Art;

use Slim\Http\Request;
use Slim\Http\Response;

class PutArt extends BaseArt
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $eid = $this->getEvent($params, 'event');
        $piece  = $params['piece'];

        $body = $request->getParsedBody();

        $sql = "SELECT `ArtistID` FROM `Artshow_DisplayArt` ";
        $sql .= "WHERE PieceID = $piece AND EventID = $eid";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll();
        if (!$this->checkArtPermission($request, 'update', $data[0]['ArtistID'])) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }

        $prices = array();

        $sql = "SELECT PriceType FROM `Artshow_PriceType` WHERE SetPrice";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll();
        foreach ($data as $priceType) {
            $prices[$priceType['PriceType']] = null;
        }

        foreach ($body as $key => $value) {
            $key = str_replace('_', ' ', $key);
            if (array_key_exists($key, $prices)) {
                $prices[$key] = $value;
            } else {
                $fields[] = " `$key` = ".\MyPDO::quote($value);
            }
        }


        $sql = "UPDATE `Artshow_DisplayArt` SET ";
        $sql .= implode(", ", $fields);
        $sql .= "WHERE PieceID = $piece AND EventID = $eid";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();

        foreach ($prices as $key => $price) {
            if ($price !== null) {
                $sql = "UPDATE `Artshow_DisplayArtPrice` SET Price = $price ";
                $sql .= "WHERE PieceID = $piece AND EventID = $eid AND ";
                $sql .= "PriceType = '$key'";
                $sth = $this->container->db->prepare($sql);
                $sth->execute();
            }
        }

        $target = new GetArt($this->container);
        $newparams = ['piece' => $piece, 'event' => $eid];
        return $target->buildResource($request, $response, $newparams);

    }


    /* end PutArt */
}
