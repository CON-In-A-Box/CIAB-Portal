<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 **/

namespace App\Modules\artshow\Controller\Utils;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\NotFoundException;
use Atlas\Query\Select;
use Atlas\Query\Update;

class GetTags extends \App\Modules\artshow\Controller\Art\BaseArt
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
        $eid = $this->getEventId($request);
        $reprint = $request->getQueryParam('reprint', false);
        $cnt = intval($request->getQueryParam('max_count', 1000000));

        $data = $this->getConfiguration([], 'Artshow_Configuration');
        foreach ($data as $entry) {
            $config[$entry['field']] = $entry['value'];
        }

        $condition = ['EventID' => $eid];
        if (!$reprint) {
            $condition['TagPrintCount'] = '0';
        }
        $data = Select::new($this->container->db)
            ->columns('*')
            ->from('Artshow_DisplayArt')
            ->whereEquals($condition)
            ->limit($cnt)
            ->fetchAll();
        if (empty($data)) {
            throw new NotFoundException('Not Found');
        }

        for ($i = 0; $i < count($data); $i++) {
            $id = $data[$i]['PieceID'];
            $aid = $data[$i]['ArtistID'];
            $prices = Select::new($this->container->db)
                ->columns('*')
                ->from('Artshow_DisplayArtPrice')
                ->whereEquals(['PieceID' => $id, 'EventID' => $eid])
                ->fetchAll();
            if (empty($prices)) {
                throw new NotFoundException('Art Prices Not Found');
            }
            $data[$i]['prices'] = $prices;

            $member = Select::new($this->container->db)
                ->columns('*')
                ->from('Members AS m')
                ->from('Artshow_Artist AS a')
                ->whereEquals(['a.ArtistID' => $aid,
                               'm.AccountID = a.AccountID'])
                ->fetchOne();
            if (empty($member)) {
                throw new NotFoundException('Member Not Found');
            }
            if ($member['CompanyNameOnSheet'] == 1) {
                $data[$i]['Artist'] = $member['CompanyName'];
            } else {
                $data[$i]['Artist'] = $member['FirstName'].' '.$member['LastName'];
            }

            $data[$i]['2dUri'] = \App\Modules\artshow\Controller\BidTag::build2DUri($request, $data[$i]);

            if ($this->container->RBAC->havePermission('api.artshow.printTags') &&
                $request->getQueryParam('official', false)) {
                $u = Update::new($this->container->db)
                    ->table('Artshow_DisplayArt')
                    ->columns(['TagPrintCount' => $data[$i]['TagPrintCount'] + 1])
                    ->whereEquals(['PieceID' => $id, 'EventID' => $eid]);
                $u->perform();
            }
        }

        if ($this->container->RBAC->havePermission('api.artshow.printTags') &&
            $request->getQueryParam('official', false)) {
            $draft = false;
        } else {
            $draft = true;
        }

        $this->bidTag = new \App\Modules\artshow\Controller\BidTag($config);
        $output = $this->bidTag->buildTags($data, $draft, array($this, 'callback'));
        $this->sendStreamPacket('END', base64_encode($output));

    }


    /* end GetTags */
}
