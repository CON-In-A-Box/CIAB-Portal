<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"artshow"},
 *      path="/artshow/artist/{artist}/tags",
 *      summary="Gets art tags for artist",
 *      @OA\Parameter(
 *          description="Id of the artist",
 *          in="path",
 *          name="artist",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/target_event",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Tags found",
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/artist_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Modules\artshow\Controller\Artist;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\NotFoundException;
use Atlas\Query\Select;
use Atlas\Query\Update;

class GetTag extends \App\Modules\artshow\Controller\Art\BaseArt
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
        $aid = $params['artist'];
        $eid = $this->getEventId($request);

        $data = $this->getConfiguration([], 'Artshow_Configuration');
        foreach ($data as $entry) {
            $config[$entry['field']] = $entry['value'];
        }

        $data = Select::new($this->container->db)
            ->columns('*')
            ->from('Artshow_DisplayArt')
            ->whereEquals(['ArtistID' => $aid, 'EventID' => $eid])
            ->fetchAll();
        if (empty($data)) {
            throw new NotFoundException('Art Not Found');
        }

        for ($i = 0; $i < count($data); $i++) {
            $id = $data[$i]['PieceID'];
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
        }

        if ($this->container->RBAC->havePermission('api.artshow.printTags') &&
            $request->getQueryParams('official', false)) {
            Update::new($this->container->db)
                ->table('Artshow_DisplayArt')
                ->columns(['TagPrintCount' => 'TagPrintCount + 1'])
                ->whereEquals(['ArtistID' => $aid, 'EventID' => $eid])
                ->perform();
            $draft = false;
        } else {
            $draft = true;
        }

        $this->bidTag = new \App\Modules\artshow\Controller\BidTag($config);
        $output = $this->bidTag->buildTags($data, $draft, array($this, 'callback'));
        $this->sendStreamPacket('END', base64_encode($output));

    }


    /* end GetTag */
}
