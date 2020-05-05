<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Artist;

use Slim\Http\Request;
use Slim\Http\Response;

class GetAccountArtist extends BaseArtist
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        if (array_key_exists('id', $params)) {
            $id = $params['id'];
        } else {
            $id = $request->getAttribute('oauth2-token')['user_id'];
        }
        $condition = "WHERE AccountID = $id";
        $sql = "SELECT * FROM `Artshow_Artist` $condition";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll();
        if (empty($result)) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Not Found', 'Artist Not Found', 404)];
        }
        $artist = $result[0];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $artist];

    }


    public function processIncludes(Request $request, Response $response, $params, $values, &$data)
    {
        if (in_array('AccountID', $values)) {
            $target = new \App\Controller\Member\GetMember($this->container);
            $newparams = $params;
            $newparams['name'] = $data['AccountID'];
            $newdata = $target->buildResource($request, $response, $newparams)[1];
            $target->processIncludes($request, $response, $params, $values, $newdata);
            $data['AccountID'] = $target->arrayResponse($request, $response, $newdata);
        }

    }


    /* end GetAccountArtist */
}
