<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Artist;

use Slim\Http\Request;
use Slim\Http\Response;

class GetTag extends \App\Modules\artshow\Controller\Art\BaseArt
{

    private $bidTag = null;


    public function buildResource(Request $request, Response $response, $params): array
    {
        $return_list = true;
        $aid = $params['artist'];
        $eid = $this->getEvent($params, 'event');

        $sql = "SELECT * FROM `Artshow_Configuration`";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll();
        $config = [];
        foreach ($data as $entry) {
            $config[$entry['Field']] = $entry['Value'];
        }

        $sql = "SELECT * FROM `Artshow_DisplayArt` WHERE ArtistID = $aid AND EventID = $eid";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll();
        if (empty($data)) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Not Found', 'Art Not Found', 404)];
        }

        for ($i = 0; $i < count($data); $i++) {
            $id = $data[$i]['PieceID'];
            $sql = "SELECT * FROM `Artshow_DisplayArtPrice` WHERE PieceID = $id AND EventID = $eid";
            $sth = $this->container->db->prepare($sql);
            $sth->execute();
            $prices = $sth->fetchAll();
            if (empty($prices)) {
                return [
                \App\Controller\BaseController::RESULT_TYPE,
                $this->errorResponse($request, $response, 'Not Found', 'Art Prices Not Found', 404)];
            }
            $data[$i]['prices'] = $prices;

            $sql = "SELECT * FROM `Members` as m, `Artshow_Artist` as a WHERE a.ArtistID = $aid AND m.AccountID = a.AccountID";
            $sth = $this->container->db->prepare($sql);
            $sth->execute();
            $member = $sth->fetchAll();
            if (empty($member)) {
                return [
                \App\Controller\BaseController::RESULT_TYPE,
                $this->errorResponse($request, $response, 'Not Found', 'Member Not Found', 404)];
            }
            $member = $member[0];
            if ($member['CompanyNameOnSheet'] == 1) {
                $data[$i]['Artist'] = $member['CompanyName'];
            } else {
                $data[$i]['Artist'] = $member['FirstName'].' '.$member['LastName'];
            }
            $data[$i]['2dUri'] = \App\Modules\artshow\Controller\BidTag::build2DUri($request, $data[$i]);
        }

        if (\ciab\RBAC::havePermission('api.artshow.printTags') &&
            $request->getQueryParams('official', false)) {
            $sql = "UPDATE `Artshow_DisplayArt` SET TagPrintCount = TagPrintCount + 1 WHERE ArtistID = $aid AND EventID = $eid";
            $sth = $this->container->db->prepare($sql);
            $sth->execute();

            $draft = false;
        } else {
            $draft = true;
        }

        $this->bidTag = new \App\Modules\artshow\Controller\BidTag($config);
        $output = $this->bidTag->buildTags($data, $draft);

        return [
        \App\Controller\BaseController::RESULT_TYPE,
        $output];

    }


    /* end GetTag */
}
