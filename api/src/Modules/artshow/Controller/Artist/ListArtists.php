<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Artist;

use Slim\Http\Request;
use Slim\Http\Response;

class ListArtists extends BaseArtist
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $sql = "SELECT ArtistID FROM `Artshow_Artist`";
        if (array_key_exists('event', $params)) {
            $sql .= " WHERE EventID = ".$params['event'];
        }
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll();
        if (empty($result)) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Not Found', 'Artists Not Found', 404)];
        }
        $output = [];
        foreach ($result as $entry) {
            $output[] = [
            'type' => 'artist_entry',
            'id' => $entry['ArtistID'],
            'get' => $request->getUri()->getBaseUrl().'/artshow/artist/'.strval($entry['ArtistID'])
            ];
        }
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $output,
        array('type' => 'artist_list')];

    }


    public function processIncludes(Request $request, Response $response, $params, $values, &$data)
    {
        if (in_array('id', $values) && in_array('id', array_keys($data))) {
            $target = new GetArtist($this->container);
            $newparams = $params;
            $newparams['artist'] = $data['id'];
            $newdata = $target->buildResource($request, $response, $newparams)[1];
            $target->processIncludes($request, $response, $params, $values, $newdata);
            $data['id'] = $target->arrayResponse($request, $response, $newdata);
        }

    }


    /* end ListArtists */
}
