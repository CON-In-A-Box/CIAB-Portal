<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Art;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\NotFoundException;
use Atlas\Query\Select;
use Atlas\Query\Update;

class GetTag extends BaseArt
{

    use \App\Controller\TraitConfiguration;
    use \App\Controller\Stream\StreamController;

    private $bidTag = null;


    public function callback($id): void
    {
        $this->sendStreamPacket($id, "In Progress");

    }


    public function doWork(Request $request, Response $response, $params, $lastEventId): void
    {
        $return_list = true;
        $id = $params['piece'];
        if ($id == 'demo') {
            $eid = '123';
        } else {
            $eid = $this->getEventId($request);
        }

        $data = $this->getConfiguration($params, 'Artshow_Configuration');
        $config = [];
        foreach ($data as $entry) {
            $config[$entry['field']] = $entry['value'];
        }

        if ($id != 'demo') {
            $data = Select::new($this->container->db)
                ->columns('*')
                ->from('Artshow_DisplayArt')
                ->whereEquals(['PieceID' => $id, 'EventID' => $eid])
                ->perform()
                ->fetchAll();
            if (empty($data)) {
                throw new NotFoundException('Art Not Found');
            }
            $data = $data[0];

            $prices = Select::new($this->container->db)
                ->columns('*')
                ->from('Artshow_DisplayArtPrice')
                ->whereEquals(['PieceID' => $id, 'EventID' => $eid])
                ->perform()
                ->fetchAll();
            if (empty($prices)) {
                throw new NotFoundException('Art Prices Not Found');
            }
            $data['prices'] = $prices;

            $aid = $data['ArtistID'];
            $member = Select::new($this->container->db)
                ->columns('*')
                ->from('Members as m')
                ->from('Artshow_Artist as a')
                ->whereEquals(['a.ArtistID' => $aid])
                ->andWhere('m.AccountID = a.AccountID')
                ->perform()
                ->fetchAll();
            if (empty($member)) {
                throw new NotFoundException('Member Not Found');
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
                $p = Select::new($this->container->db)
                    ->columns('*')
                    ->from('Artshow_PriceType')
                    ->whereEquals(['SetPrice' => 1])
                    ->perform()
                    ->fetchAll();
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

        if ($this->container->RBAC->havePermission('api.artshow.printTags') &&
            $request->getQueryParams('official', false)) {
            Update::new($this->container->db)
                ->table('Artshow_DisplayArt')
                ->columns(['TagPrintCount' => 'TagPrintCount + 1'])
                ->whereEquals(['PieceID' => $id, 'EventID' => $eid])
                ->perform();
            $draft = false;
        } else {
            $draft = true;
        }

        $this->bidTag = new \App\Modules\artshow\Controller\BidTag($config);
        $this->sendStreamPacket('END', base64_encode($this->bidTag->buildTags($dataset, $draft, array($this, 'callback'))));

    }


    /* end GetTag */
}
