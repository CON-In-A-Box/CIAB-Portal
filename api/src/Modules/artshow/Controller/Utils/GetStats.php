<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 **/

namespace App\Modules\artshow\Controller\Utils;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Container;
use App\Controller\NotFoundException;
use Atlas\Query\Select;

class GetStats extends \App\Modules\artshow\Controller\BaseArtshow
{


    public function __construct(Container $container)
    {
        parent::__construct('stats', $container);

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $eid = $this->getEventId($request);
        $output = [
            'type' => 'show_stats'
        ];

        $data = Select::new($this->container->db)
            ->columns('COUNT(ArtistID) AS count')
            ->from('Artshow_Artist')
            ->fetchOne();
        $output['artist_count'] = $data['count'];

        $data = Select::new($this->container->db)
            ->columns('COUNT(ArtistID) AS count')
            ->from('Artshow_Registration')
            ->whereEquals(['EventID' => $eid])
            ->fetchOne();
        $output['event_artist_count'] = $data['count'];

        $data = Select::new($this->container->db)
            ->columns('COUNT(PieceID) AS count')
            ->from('Artshow_DisplayArt')
            ->whereEquals(['EventID' => $eid])
            ->fetchOne();
        $output['event_hung_count'] = $data['count'];

        $data = Select::new($this->container->db)
            ->columns('COUNT(PieceID) AS count')
            ->from('Artshow_PrintShopArt')
            ->whereEquals(['EventID' => $eid])
            ->fetchOne();
        $output['event_print_count'] = $data['count'];

        $data = Select::new($this->container->db)
            ->columns('COUNT(SaleID) AS count')
            ->columns('SUM(Price) AS total')
            ->from('Artshow_Art_Sale')
            ->whereEquals(['EventID' => $eid])
            ->fetchOne();
        $output['event_hung_sale_count'] = $data['count'];
        $output['event_hung_sale_total'] = $data['total'];

        $data = Select::new($this->container->db)
            ->columns('COUNT(SaleID) AS count')
            ->columns('SUM(Price) AS total')
            ->from('Artshow_Print_Sale')
            ->whereEquals(['EventID' => $eid])
            ->fetchOne();
        $output['event_print_sale_count'] = $data['count'];
        $output['event_print_sale_total'] = $data['total'];

        $data = Select::new($this->container->db)
            ->columns('COUNT(PieceID) as count')
            ->from('Artshow_DisplayArt')
            ->whereEquals(['EventID' => $eid, 'TagPrintCount' => 0])
            ->fetchOne();
        $output['unprinted_tag_count'] = $data['count'];

        return [
        \App\Controller\BaseController::RESULT_TYPE,
        $output];

    }


    /* end GetStats */
}
