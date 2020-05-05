<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Art;

use Slim\Http\Request;
use Slim\Http\Response;

class GetTag extends BaseArt
{

    use \App\Controller\TraitConfiguration;

    private $bidTag = null;


    public function buildResource(Request $request, Response $response, $params): array
    {
        $return_list = true;
        $id = $params['piece'];
        if ($id == 'demo') {
            $eid = '123';
        } else {
            $eid = $this->getEvent($params, 'event');
        }

        $data = $this->getConfiguration($params, 'Artshow_Configuration');
        $config = [];
        foreach ($data as $entry) {
            $config[$entry['field']] = $entry['value'];
        }

        if ($id != 'demo') {
            $sql = "SELECT * FROM `Artshow_DisplayArt` WHERE PieceID = $id AND EventID = $eid";
            $sth = $this->container->db->prepare($sql);
            $sth->execute();
            $data = $sth->fetchAll();
            if (empty($data)) {
                return [
                \App\Controller\BaseController::RESULT_TYPE,
                $this->errorResponse($request, $response, 'Not Found', 'Art Not Found', 404)];
            }
            $data = $data[0];

            $sql = "SELECT * FROM `Artshow_DisplayArtPrice` WHERE PieceID = $id AND EventID = $eid";
            $sth = $this->container->db->prepare($sql);
            $sth->execute();
            $prices = $sth->fetchAll();
            if (empty($prices)) {
                return [
                \App\Controller\BaseController::RESULT_TYPE,
                $this->errorResponse($request, $response, 'Not Found', 'Art Prices Not Found', 404)];
            }
            $data['prices'] = $prices;

            $aid = $data['ArtistID'];
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
                $data['Artist'] = $member['CompanyName'];
            } else {
                $data['Artist'] = $member['FirstName'].' '.$member['LastName'];
            }
            $data['2dUri'] = \App\Modules\artshow\Controller\BidTag::build2DUri($request, $data);
            $dataset = [$data];
        } else {
            $dataset = [];
            for ($i = 0; $i < 12; $i++) {
                $dataset[$i] = [
                'PieceID' => "$i",
                'EventID' => '123',
                'ArtistID' => '123',
                'Name' => 'Awesome Example Art '.$i,
                'Medium' => 'Bits on Paper',
                'PieceType' => 'Normal',
                'Edition' => '1st',
                'NFS' => '0',
                'Charity' => '0',
                'NonTax' => '0',
                'Artist' => 'Artie Aarrtist'
                ];

                $prices = [];
                $sql = "SELECT * FROM `Artshow_PriceType` WHERE SetPrice = 1";
                $sth = $this->container->db->prepare($sql);
                $sth->execute();
                $p = $sth->fetchAll();
                foreach ($p as $price) {
                    $prices[] = [
                    'PriceType' => $price['PriceType'],
                    'Price' => rand(1, 200)
                    ];
                }

                $dataset[$i]['prices'] = $prices;
                $dataset[$i]['2dUri'] = \App\Modules\artshow\Controller\BidTag::build2DUri($request, $dataset[$i]);
            }
        }

        if (\ciab\RBAC::havePermission('api.artshow.printTags') &&
            $request->getQueryParams('official', false)) {
            $sql = "UPDATE `Artshow_DisplayArt` SET TagPrintCount = TagPrintCount + 1 WHERE PieceID = $id AND EventID = $eid";
            $sth = $this->container->db->prepare($sql);
            $sth->execute();

            $draft = false;
        } else {
            $draft = true;
        }

        $this->bidTag = new \App\Modules\artshow\Controller\BidTag($config);
        $output = $this->bidTag->buildTags($dataset, $draft);

        return [
        \App\Controller\BaseController::RESULT_TYPE,
        $output];

    }


    /* end GetTag */
}
