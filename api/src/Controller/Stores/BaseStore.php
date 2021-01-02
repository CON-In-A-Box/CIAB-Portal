<?php declare(strict_types=1);

namespace App\Controller\Stores;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\BaseController;

abstract class BaseStore extends BaseController
{

    /**
     * @var int
     */
    protected $id = 0;


    public function __construct(Container $container)
    {
        parent::__construct('stores', $container);
        
    }


    protected function buildStoresHateoas(Request $request)
    {
        if ($this->id !== 0) {
            $path = $request->getUri()->getBaseUrl();
            $this->addHateoasLink('self', $path.'/stores/'.strval($this->id), 'GET');
        }

    }


    protected function getStore(/*.array.*/&$stores, $params)
    {
        $sth = $this->container->db->prepare("SELECT * FROM 'Stores' WHERE 'StoreID' =  ".$params['id']);
        $sth->execute();
        $stores = $sth->fetchAll();
        if (empty($stores)) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Not Found', 'Store Not Found', 404)];
        }

        return null;
        
    }


    /* End BaseStores */
}
