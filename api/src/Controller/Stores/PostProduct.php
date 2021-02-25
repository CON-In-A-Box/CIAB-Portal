<?php declare(strict_types=1);

namespace App\Controller\Stores;

use Atlas\Query\Insert;
use Atlas\Query\Select;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\BaseController;
use App\Controller\PermissionDeniedException;
use App\Controller\InvalidParameterException;

class PostProduct extends BaseProduct
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        // TODO: RBAC more than just admin maybe
        if (!$_SESSION['IS_ADMIN']) {
            throw new PermissionDeniedException();
        }

        $body = $request->getParsedBody();

        $store_select = Select::new($this->container->db);
        $store_id = $params['store_id'];
        $store = $store_select->columns('StoreID')->from('Stores')->whereEquals(['StoreID' => $store_id]);
        if (empty($store)) {
            return [
            BaseController::RESULT_TYPE,
            $this->notFoundResponse($request, $response, 'Store', $store_id)
            ];
        }

        if (!empty($body)) {
            $required = ['Name', 'ProductSlug', 'Description', 'UnitPriceCents'];
            $diff = array_diff($required, array_keys($body));
            if (count($diff)) {
                throw new InvalidParameterException("Missing required parameters: ".implode(', ', $diff));
            }
        } else {
            throw new InvalidParameterException('No data provided');
        }

        $body['StoreID'] = $store_id;
        
        if (!array_key_exists('PaymentSystemRef', $body)) {
            $body['PaymentSystemRef'] = null;
        }

        $permitted_params = ['StoreID', 'Name', 'ProductSlug', 'Description', 'UnitPriceCents', 'PaymentSystemRef'];
        $body = $this->filterBodyParams($permitted_params, $body);

        $insert = Insert::new($this->container->db);
        $result = $insert->into('Products')->columns($body)->perform();

        $id = $insert->getLastInsertId();

        $product = $this->getProduct(array('id' => $id), $request, $response, $error);
        
        $output = array(
            'type' => 'product',
            'data' => $product
        );

        return [
        BaseController::RESOURCE_TYPE,
        $output,
        201
        ];

    }
    

    /* end PostProduct */
}
