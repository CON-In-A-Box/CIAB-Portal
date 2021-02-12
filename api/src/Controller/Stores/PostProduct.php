<?php declare(strict_types=1);

namespace App\Controller\Stores;

use Atlas\Query\Insert;
use Atlas\Query\Select;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\BaseController;

class PostProduct extends BaseProduct
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        // TODO: RBAC more than just admin maybe
        if (!$_SESSION['IS_ADMIN']) {
            return [
            BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
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
                return [
                BaseController::RESULT_TYPE,
                $this->errorResponse($request, $response, "Missing required parameters: ".implode(', ', $diff), 'Bad parameters', 400)
                ];
            }
        } else {
            return [
            BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'No data provided', 'Bad parameters', 400)
            ];
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
