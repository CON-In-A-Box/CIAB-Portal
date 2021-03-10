<?php declare(strict_types=1);

namespace App\Controller\Stores;

use Atlas\Query\Select;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

use App\Controller\BaseController;
use App\Controller\NotFoundException;

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


    protected function getStore(array $params, Request $request, Response $response, &$error)
    {
        $select = Select::new($this->container->db);
        $store = $select->columns('StoreID as id', 'Name', 'StoreSlug', 'Description')->from('Stores')->whereEquals(['StoreID' => $params['id']])->fetchOne();

        if (empty($store)) {
            throw new NotFoundException("Could not find Store ID ${params['id']}");
        }

        return $store;

    }


    /* End BaseStores */
}
