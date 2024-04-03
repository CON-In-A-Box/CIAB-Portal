<?php declare(strict_types=1);

/**
 *  @OA\Get(
 *      tags={"stores"},
 *      path="/stores",
 *      summary="Lists stores",
 *      @OA\Response(
 *          response=200,
 *          description="OK",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/store_list"
 *          )
 *      )
 *  )
 */

namespace App\Controller\Stores;

use Atlas\Query\Select;

use Slim\Http\Request;
use Slim\Http\Response;

class ListStores extends BaseStore
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $select = Select::new($this->container->db);
        $stores = $select->columns(...BaseStore::selectMapping())->from('Stores')->fetchAll();

        $output = array();
        $output['type'] = 'store_list';
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $stores,
        $output];

    }


    /* end ListStores */
}
