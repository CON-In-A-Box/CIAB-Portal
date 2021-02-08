<?php declare(strict_types=1);

namespace App\Controller\Stores;

use Atlas\Query\Select;
use Atlas\Query\Update;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\BaseController;

class PutStore extends BaseStore
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        // TODO: RBAC more than just admin maybe
        if (!$_SESSION['IS_ADMIN']) {
            return [
            BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }
        
        $store = $this->getStore($params, $request, $response, $error);

        if (empty($store)) {
            return $error;
        }

        $body = $request->getParsedBody();
        
        # Filter for keys we accept. TODO: extract as a base clas function?
        $permitted_keys = ['Name', 'StoreSlug', 'Description'];
        $body = $this->filterBodyParams($permitted_keys, $body);
        
        $update = Update::new($this->container->db);
        $update->table('Stores')->columns($body);
        $update->whereEquals(['StoreID' => $params['id']]);
        $update->perform();
        
        $store = $this->getStore($params, $request, $response, $error);

        if (empty($store)) {
            return $error;
        }
        
        return [
        BaseController::RESOURCE_TYPE,
        $store
        ];

    }

    
    /* end PutStore */
}
