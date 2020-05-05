<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Show;

use Slim\Http\Request;
use Slim\Http\Response;

class PutArtistShow extends BaseShow
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $body = $request->getParsedBody();
        $eid = $this->getEvent($params, 'event');
        $id = $params['artist'];

        if (!$this->checkPermission($request, 'update', $id)) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }

        $fields = array();
        $len = strlen('custom_question_');
        $custom = [];
        foreach ($body as $key => $value) {
            if (substr($key, 0, $len) === 'custom_question_') {
                $custom[substr($key, $len)] = \MyPDO::quote($value);
            } else {
                $fields[$key] = \MyPDO::quote($value);
            }
        }
        if (array_key_exists('MailIn', $body)) {
            $fields['MailIn'] = (boolval($body['MailIn']) ? '1' : '0');
        }

        $data = array();
        foreach ($fields as $key => $value) {
            $data[] = "`$key`=$value";
        }

        $sql = "UPDATE `Artshow_Registration` SET ";
        $sql .= implode(", ", $data);
        $sql .= " WHERE ArtistID = $id AND EventID = $eid";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();

        foreach ($custom as $key => $value) {
            $sql = "UPDATE `Artshow_RegistrationAnswer` SET ";
            $sql .= "`Answer` = $value";
            $sql .= " WHERE ArtistID = $id AND EventID = $eid AND QuestionID = $key";
            $sth = $this->container->db->prepare($sql);
            $sth->execute();
        }


        $target = new GetArtistShow($this->container);
        $newparams = ['artist' => $id, 'event' => $eid];
        return $target->buildResource($request, $response, $newparams);

    }


    /* end PutArtistShow */
}
