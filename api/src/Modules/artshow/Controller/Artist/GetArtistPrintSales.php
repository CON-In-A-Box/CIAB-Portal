<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Artist;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\IncludeResource;
use App\Controller\NotFoundException;
use Atlas\Query\Select;

class GetArtistPrintSales extends \App\Modules\artshow\Controller\Sale\BaseSale
{


    public function __construct($container)
    {
        parent::__construct($container);
        $this->includes = [
        new IncludeResource('\App\Controller\Member\GetMember', 'id', 'buyer'),
        new IncludeResource('\App\Controller\Event\GetEvent', 'id', 'event'),
        new IncludeResource('\App\Modules\artshow\Controller\PrintArt\GetPrint', 'piece', 'piece')
        ];

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $eid = $this->getEventId($request);

        $data = Select::new($this->container->db)
            ->columns(...\App\Modules\artshow\Controller\Sale\BaseSale::selectMapping())
            ->from('Artshow_Print_Sale')
            ->whereEquals(['EventID' => $eid])
            ->andWhere("PieceID IN (Select PieceID FROM Artshow_PrintShopArt where ArtistID = ".$params['artist'].")")
            ->fetchAll();
        if (empty($data) && empty($data2)) {
            throw new NotFoundException('Sales Not Found');
        }
        $output = array();
        $output['type'] = 'artshow_sale_list';
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        $output];

    }


    /* end GetArtistPrintSales */
}
