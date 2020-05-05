<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Artist;

use Slim\Http\Request;
use Slim\Http\Response;

class PutArtist extends BaseArtist
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $artist = $params['artist'];

        if (!$this->checkArtistPermission($request, 'update', $artist)) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }

        $body = $request->getParsedBody();

        $fields = array();
        foreach ($body as $key => $value) {
            $key = str_replace('_', ' ', $key);
            $fields[] = " `$key` = ".\MyPDO::quote($value);
        }

        if (count($fields) > 0) {
            $sql = "UPDATE `Artshow_Artist` SET ";
            $sql .= implode(", ", $fields);
            $sql .= " WHERE ArtistID = $artist";
            $sth = $this->container->db->prepare($sql);
            $sth->execute();
        }

        $target = new GetArtist($this->container);
        $data = $target->buildResource($request, $response, ['artist' => $artist])[1];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data)
        ];

    }


    /* end PutArtist */
}
