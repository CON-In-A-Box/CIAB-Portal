<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\PrintArt;

use Slim\Http\Request;
use Slim\Http\Response;

class GetPrint extends BasePrint
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $return_list = true;
        $eid = $this->getEvent($params, 'event');

        if (array_key_exists('piece', $params)) {
            $return_list = false;
            $sql = "SELECT * FROM `Artshow_PrintShopArt` ";
            $id = $params['piece'];
            $sql .= "WHERE PieceID = $id AND EventID = $eid";
        } else {
            $sql = "SELECT EventID, PieceID FROM `Artshow_PrintShopArt` ";
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
            $this->errorResponse($request, $response, 'Not Found', 'Print Art Not Found', 404)];
        }

        if (!$return_list) {
            $this->buildHateoas($request, $result[0]);
            return [
            \App\Controller\BaseController::RESOURCE_TYPE,
            $result[0]];
        }
        $data = [];
        foreach ($result as $entry) {
            $data[] = [
            'type' => "print_entry",
            'id' => $entry['PieceID'],
            'event' => $eid,
            'get' => $request->getUri()->getBaseUrl().'/artshow/print/'.strval($entry['PieceID']).'/'.strval($entry['EventID'])
            ];
        }
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        array('type' => 'print_list')];

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
            $target = new GetPrint($this->container);
            $newparams = $params;
            $newparams['piece'] = $data['id'];
            $newparams['event'] = $data['event'];
            $newdata = $target->buildResource($request, $response, $newparams)[1];
            $target->processIncludes($request, $response, $params, $values, $newdata);
            $data['id'] = $target->arrayResponse($request, $response, $newdata);
        }

    }


    /* end GetPrint */
}
