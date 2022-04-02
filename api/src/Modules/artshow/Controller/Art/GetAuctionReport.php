<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Art;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\IncludeResource;
use App\Controller\NotFoundException;
use Atlas\Query\Select;

class GetAuctionReport extends BaseArt
{
    use \App\Controller\Stream\StreamController;


    public function __construct($container)
    {
        parent::__construct($container);
        $this->includes = [
        new IncludeResource('\App\Modules\artshow\Controller\Artist\GetArtist', 'artist', 'artist'),
        new IncludeResource('\App\Controller\Event\GetEvent', 'id', 'event')
        ];

    }


    public function doWork(Request $request, Response $response, $args, $lastEventId): void
    {
        $eid = $this->getEventId($request);

        do {
            $data = Select::new($this->container->db)
                ->columns(...BaseArt::selectMapping())
                ->from('Artshow_DisplayArt')
                ->whereEquals(['EventID' => $eid, 'inAuction' => 1])
                ->orderBy('PieceID')
                ->perPage(1)
                ->page($lastEventId)
                ->fetchAll();

            if (empty($data)) {
                break;
            }

            $result = json_encode($this->expandIncludes($request, $response, $args, $data));
            $this->sendStreamPacket($lastEventId, $result);
            $lastEventId += 1;
        } while (true);

        $this->endStream();

    }


    /* end GetAuctionReport */
}
