<?php declare(strict_types=1);

namespace App\Controller\Stores;

use Atlas\Query\Select;
use Atlas\Query\Update;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\BaseController;

class PutProduct extends BaseProduct
{
    

    public function buildResource(Request $request, Response $response, $params): array
    {
        // TODO: RBAC more than just admin maybe
        if (!$_SESSION['IS_ADMIN']) {
            return [
            BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }

        $product = $this->getProduct($params, $request, $response, $error);

        if (empty($product)) {
            return $error;
        }

        $body = $request->getParsedBody();

        $permitted_keys = ['Name', 'StoreSlug', 'Description', 'UnitPriceCents'];
        $body = $this->filterBodyParams($permitted_keys, $body);

        $update = Update::new($this->container->db);
        $update->table('Products')->columns($body);
        $update->whereEquals(['ProductID' => $params['id']]);
        $update->perform();

        $product = $this->getProduct($params, $request, $response, $error);

        if (empty($product)) {
            return $error;
        }

        return [
        BaseController::RESOURCE_TYPE,
        $product
        ];

    }

    
    /* end PutProduct */
}
