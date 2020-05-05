<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Show;

use Slim\Http\Request;
use Slim\Http\Response;

class PostArtistShow extends BaseShow
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $body = $request->getParsedBody();
        $eid = $this->getEvent($params, 'event');
        $id = $params['artist'];

        if (!$this->checkPermission($request, 'add', $id)) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }

        $fields = [
        'ArtistID' => $id,
        'EventID' => $eid,
                  ];

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

        $sql = "INSERT INTO `Artshow_Registration` (";
        $sql .= implode(", ", array_keys($fields));
        $sql .= ") VALUES (";
        $sql .= implode(", ", array_values($fields));
        $sql .= ")";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();

        foreach ($custom as $key => $value) {
            $sql = "INSERT INTO `Artshow_RegistrationAnswer` (";
            $sql .= "`ArtistID`, `EventID`, `QuestionID`, `Answer`";
            $sql .= ") VALUES (";
            $sql .= $id.', '.$eid.', '.$key.', '.$value;
            $sql .= ")";
            $sth = $this->container->db->prepare($sql);
            $sth->execute();
        }

        $target = new GetArtistShow($this->container);
        $newparams = ['artist' => $fields['ArtistID'], 'event' => $fields['EventID']];
        return $target->buildResource($request, $response, $newparams);

    }


    /* end PostArtistShow */
}
