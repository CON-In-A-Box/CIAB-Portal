<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Show;

use Slim\Http\Request;
use Slim\Http\Response;

class GetArtistShow extends BaseShow
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $eid = $this->getEvent($params, 'event');
        $id = $params['artist'];
        $sql = "SELECT * FROM `Artshow_Registration` WHERE ArtistID = $id AND EventID = $eid";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll();
        if (empty($result)) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Not Found', 'Artist Event Registration Not Found', 404)];
        }
        $artist = $result[0];

        $sql = "SELECT * FROM `Artshow_RegistrationAnswer` WHERE ArtistID = $id AND EventID = $eid";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll();
        foreach ($result as $value) {
            $artist['custom_question_'.$value['QuestionID']] = $value['Answer'];
        }

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $artist];

    }


    /* end GetArtistShow */
}
