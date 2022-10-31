<?php declare(strict_types=1);

namespace App\Controller\Stores;

use Atlas\Query\Select;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

use App\Controller\BaseController;
use App\Controller\NotFoundException;

abstract class BaseProduct extends BaseController
{
    
    /**
     * @var int
     */
    protected $id = 0;


    public function __construct(Container $container)
    {
        parent::__construct('products', $container);
        
    }


    protected function getProduct(array $params, Request $request, Response $response, &$error)
    {
        $select = Select::new($this->container->db);
        $select->columns('ProductID as id', 'StoreID', 'Name', 'ProductSlug', 'Description', 'UnitPriceCents', 'PaymentSystemRef');
        $select->from('Products')->whereEquals(['ProductID' => $params['id']]);
        $product = $select->fetchOne();

        if (empty($product)) {
            throw new NotFoundException("Could not find Product with ID ${params['id']}");
        }

        return $product;

    }


    /* End BaseProduct */
}
