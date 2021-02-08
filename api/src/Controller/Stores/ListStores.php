<?php declare(strict_types=1);

namespace App\Controller\Stores;

use Atlas\Query\Select;

use Slim\Http\Request;
use Slim\Http\Response;

class ListStores extends BaseStore
{
    

    public function buildResource(Request $request, Response $response, $args): array
    {
        $select = Select::new($this->container->db);
        $stores = $select->columns('StoreID as id', 'Name', 'StoreSlug', 'Description')->from('Stores')->fetchAll();

        $output = array();
        $output['type'] = 'stores_list';
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $stores,
        $output];
        
    }


    /* end ListStores */
}
