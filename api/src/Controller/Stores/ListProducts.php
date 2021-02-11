<?php declare(strict_types=1);

namespace App\Controller\Stores;

use Atlas\Query\Select;
use Slim\Http\Request;
use Slim\Http\Response;

class ListProducts extends BaseProduct
{
    

    public function buildResource(Request $request, Response $response, $params): array
    {
        $select = Select::new($this->container->db);
        $select->columns('ProductID as id', 'StoreID', 'Name', 'ProductSlug', 'Description', 'UnitPriceCents', 'PaymentSystemRef');
        $select->from('Products')->whereEquals(['StoreID' => $params['store_id']]);
        $products = $select->fetchAll();
        
        $output = array();
        $output['type'] = 'products_list';
        
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $products,
        $output
        ];

    }


    /* end ListProducts */
}
