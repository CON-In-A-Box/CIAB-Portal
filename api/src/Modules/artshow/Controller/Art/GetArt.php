<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Art;

use Slim\Http\Request;
use Slim\Http\Response;

class GetArt extends BaseArt
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $return_list = true;
        $eid = $this->getEvent($params, 'event');

        if (array_key_exists('piece', $params)) {
            $return_list = false;
            $id = $params['piece'];
            $sql = "SELECT * FROM `Artshow_DisplayArt` ";
            $sql .= "JOIN `Artshow_DisplayArtPrice` ON Artshow_DisplayArtPrice.PieceID = Artshow_DisplayArt.PieceID AND "."Artshow_DisplayArtPrice.EventID = Artshow_DisplayArt.EventID ";
            $sql .= "WHERE Artshow_DisplayArt.PieceID = $id AND Artshow_DisplayArt.EventID = $eid";
        } else {
            $sql = "SELECT EventID, PieceID FROM `Artshow_DisplayArt` ";
            if (array_key_exists('artist', $params)) {
                $artist = $params['artist'];
                $sql .= "WHERE ArtistID = $artist AND EventID= $eid";
            } else {
                $sql .= "WHERE EventID = $eid";
            }
            $sql .= " ORDER BY PieceID ASC";
        }

        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll();
        if (empty($result)) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Not Found', 'Art Not Found', 404)];
        }
        $output = array();
        foreach ($result as $art) {
            if (array_key_exists($art['PieceID'], $output)) {
                $output[$art['PieceID']][$art['PriceType']] = $art['Price'];
            } else {
                $output[$art['PieceID']] = $this->buildArt(
                    $request,
                    $response,
                    $art
                );
            }
        }
        $output = array_values($output);
        if (!$return_list) {
            $this->buildArtHateoas($request, $result[0]);
            return [
            \App\Controller\BaseController::RESOURCE_TYPE,
            $output[0]];
        }
        $data = [];
        foreach ($output as $entry) {
            $data[] = [
            'type' => "art_entry",
            'id' => $entry['PieceID'],
            'event' => $eid,
            'get' => $request->getUri()->getBaseUrl().'/artshow/art/piece/'.strval($entry['PieceID']).'/'.strval($entry['EventID'])
            ];
        }
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        array('type' => 'art_list')];

    }


    public function processIncludes(Request $request, Response $response, $params, $values, &$data)
    {
        if (in_array('ArtistID', $values) && in_array('ArtistID', array_keys($data))) {
            $target = new \App\Modules\artshow\Controller\Artist\GetArtist($this->container);
            $newparams = $params;
            $newparams['artist'] = $data['ArtistID'];
            $newdata = $target->buildResource($request, $response, $newparams)[1];
            $target->processIncludes($request, $response, $params, $values, $newdata);
            $data['ArtistID'] = $target->arrayResponse($request, $response, $newdata);
        }
        if (in_array('id', $values) && in_array('id', array_keys($data))) {
            $target = new GetArt($this->container);
            $newparams = $params;
            $newparams['piece'] = $data['id'];
            $newparams['event'] = $data['event'];
            $newdata = $target->buildResource($request, $response, $newparams)[1];
            $target->processIncludes($request, $response, $params, $values, $newdata);
            $data['id'] = $target->arrayResponse($request, $response, $newdata);
        }

    }


    /* end GetArt */
}
