<?php declare(strict_types=1);

namespace App\Controller\Stores;

use Atlas\Query\Select;

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


    protected function getStore(array $params, Request $request, Response $response, &$error)
    {
        $select = Select::new($this->container->db);
        $store = $select->columns('StoreID as id', 'Name', 'StoreSlug', 'Description')->from('Stores')->whereEquals(['StoreID' => $params['id']])->fetchOne();

        if (empty($store)) {
            $error = [
            BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, "Could not find Store ID ${params['id']}", 'Not found', 404)
            ];
            return null;
        }

        return $store;

    }


    /* End BaseStores */
}
