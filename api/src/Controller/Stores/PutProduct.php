<?php declare(strict_types=1);

namespace App\Controller\Stores;

use Atlas\Query\Select;
use Atlas\Query\Update;
use Slim\Http\Request;
use Slim\Http\Response;

use App\Controller\BaseController;
use App\Controller\NotFoundException;

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

        $body = $request->getParsedBody();

        $permitted_keys = ['Name', 'StoreSlug', 'Description', 'UnitPriceCents'];
        $body = $this->filterBodyParams($permitted_keys, $body);

        $update = Update::new($this->container->db);
        $update->table('Products')->columns($body);
        $update->whereEquals(['ProductID' => $params['id']]);
        $result = $update->perform();

        if ($result->rowCount() == 0) {
            throw new NotFoundException("Product ID ${params['id']} does not exist");
        }

        $product = $this->getProduct($params, $request, $response, $error);

        return [
        BaseController::RESOURCE_TYPE,
        $product
        ];

    }

    
    /* end PutProduct */
}
