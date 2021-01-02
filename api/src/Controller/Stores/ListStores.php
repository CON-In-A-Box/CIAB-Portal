<?php declare(strict_types=1);

namespace App\Controller\Stores;

use Slim\Http\Request;
use Slim\Http\Response;

class ListStores extends BaseStore
{
    

    public function buildResource(Request $request, Response $response, $args): array
    {
        $sql = "SELECT * FROM `Stores`";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $stores = $sth->fetchAll();
        $output = array();
        $output['type'] = 'stores_list';
        $data = array();
        foreach ($stores as $store) {
            $store['type'] = 'store';
            $store['id'] = $store['StoreID'];
            unset($store['StoreID']);
            $data[] = $store;
        }
        return [
            \App\Controller\BaseController::LIST_TYPE,
            $data,
            $output];
    }


    /* end ListStores */
}
