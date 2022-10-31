<?php declare(strict_types=1);

namespace App\Controller\Stores;

use Atlas\Query\Select;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\BaseController;
use App\Controller\PermissionDeniedException;

class GetProduct extends BaseProduct
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        // TODO: RBAC more than just admin maybe
        if (!$_SESSION['IS_ADMIN']) {
            throw new PermissionDeniedException();
        }

        $product = $this->getProduct($params, $request, $response, $error);
        
        return [
        BaseController::RESOURCE_TYPE,
        $product
        ];

    }


    /* end GetProduct */
}
