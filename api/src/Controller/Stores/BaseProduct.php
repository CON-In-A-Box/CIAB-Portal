<?php declare(strict_types=1);

namespace App\Controller\Stores;

use Atlas\Query\Select;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\BaseController;

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


    protected function buildProductHateoas(Request $request, Number $store_id)
    {
        if ($this->id !== 0) {
            $path = $request->getUri()->getBaseUrl();
            $this->addHateoasLink('self', $path.'/stores/'.strval($store_id).'/products/'.strval($this->id), 'GET');
        }

    }


    protected function getProduct(array $params, Request $request, Response $response, &$error)
    {
        $select = Select::new($this->container->db);
        $select->columns('ProductID as id', 'StoreID', 'Name', 'ProductSlug', 'Description', 'UnitPriceCents', 'PaymentSystemRef');
        $select->from('Products')->whereEquals(['ProductID' => $params['id']]);
        $product = $select->fetchOne();

        if (empty($product)) {
            $error = [
            BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, "Could not find Product with ID ${params['id']}", 'Not found', 404)
            ];
            
            return null;
        }

        return $product;

    }


    /* End BaseProduct */
}
