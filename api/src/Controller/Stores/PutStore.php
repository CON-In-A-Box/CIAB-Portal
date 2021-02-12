<?php declare(strict_types=1);

namespace App\Controller\Stores;

use Atlas\Query\Select;
use Atlas\Query\Update;
use Slim\Http\Request;
use Slim\Http\Response;

use App\Controller\BaseController;
use App\Controller\NotFoundException;

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
        
        $body = $request->getParsedBody();
        
        # Filter for keys we accept. TODO: extract as a base clas function?
        $permitted_keys = ['Name', 'StoreSlug', 'Description'];
        $body = $this->filterBodyParams($permitted_keys, $body);
        
        $update = Update::new($this->container->db);
        $update->table('Stores')->columns($body);
        $update->whereEquals(['StoreID' => $params['id']]);
        $result = $update->perform();
        
        if ($result->rowCount() == 0) {
            throw new NotFoundException("Store ID ${params['id']} does not exist");
        }
        
        $store = $this->getStore($params, $request, $response, $error);
        
        return [
        BaseController::RESOURCE_TYPE,
        $store
        ];

    }

    
    /* end PutStore */
}
