<?php declare(strict_types=1);

namespace App\Controller\Stores;

use Atlas\Query\Insert;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\PermissionDeniedException;
use App\Controller\InvalidParameterException;

class PostStore extends BaseStore
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        // TODO: RBAC more than just admin maybe
        if (!$_SESSION['IS_ADMIN']) {
            throw new PermissionDeniedException();
        }

        $body = $request->getParsedBody();
        if (!array_key_exists('Name', $body)) {
            throw new InvalidParameterException('Required \'Name\' parameter not present');
        }

        $body['StoreID'] = null;
        $permitted_params = ['StoreID', 'Name', 'StoreSlug', 'Description'];
        $body = $this->filterBodyParams($permitted_params, $body);

        $insert = Insert::new($this->container->db);
        $insert->into('Stores')->columns($body);
        
        $sth = $insert->perform();
        $id = $insert->getLastInsertId();

        $store = $this->getStore(array('id' => $id), $request, $response, $error);

        $output = array(
            'type' => 'store',
            'data' => $store
        );

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $output,
        201
        ];

    }


    /* end PostStore */
}
