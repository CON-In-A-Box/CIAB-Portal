<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Artist;

use Slim\Http\Request;
use Slim\Http\Response;

class PostArtist extends BaseArtist
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $fields = array();
        $user = $request->getAttribute('oauth2-token')['user_id'];
        $body = $request->getParsedBody();

        if (!$body || !array_key_exists('AccountID', $body)) {
            $accountID = $user;
        } else {
            $accountID = intval($body['AccountID']);
        }

        if (($accountID != $user) &&
            (!\ciab\RBAC::havePermission("api.post.artshow.artist"))) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }

        foreach ($body as $key => $value) {
            $fields[$key] = \MyPDO::quote($value);
        }
        $fields['AccountID'] = $accountID;

        $sql = "INSERT INTO `Artshow_Artist` (";
        $sql .= implode(", ", array_keys($fields));
        $sql .= ") VALUES (";
        $sql .= implode(", ", array_values($fields));
        $sql .= ")";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();

        $sql = "SELECT * FROM `Artshow_Artist` WHERE ArtistID = (SELECT LAST_INSERT_ID())";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll();
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $result[0]];

    }


    /* end PostArtist */
}
