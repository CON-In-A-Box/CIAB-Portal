<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Sale;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\IncludeResource;
use App\Controller\NotFoundException;
use Atlas\Query\Select;

class GetCustomerArtSales extends BaseSale
{


    public function __construct($container)
    {
        parent::__construct($container);
        $this->includes = [
        new IncludeResource('\App\Controller\Member\GetMember', 'id', 'buyer'),
        new IncludeResource('\App\Controller\Event\GetEvent', 'id', 'event'),
        new IncludeResource('\App\Modules\artshow\Controller\Art\GetArt', 'piece', 'piece')
        ];

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $customer = $this->getBuyer($request, $response, $params['id']);
        $eid = $this->getEventId($request);

        $data = Select::new($this->container->db)
            ->columns(...BaseSale::selectMapping())
            ->from('Artshow_Art_Sale')
            ->whereEquals(['BuyerID' => $customer[0]['id'], 'EventID' => $eid])
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


    /* end GetCustomerArtSales */
}
