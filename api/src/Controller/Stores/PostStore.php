<?php declare(strict_types=1);

namespace App\Controller\Stores;

use Slim\Http\Request;
use Slim\Http\Response;

class PostStore extends BaseStore
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        // TODO: RBAC more than just admin maybe
        if (!$_SESSION['IS_ADMIN']) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }

        $body = $request->getParsedBody();
        if (!array_key_exists('store_name', $body)) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Required \'store_name\' parameter not present', 'Missing Parameter', 400)
            ];
        }
        $sql = <<<SQL
        INSERT INTO `Stores` (`StoreID`, `Name`, `StoreSlug`, `Description`)
        VALUES (NULL, '${body["store_name"]}', '${body["store_slug"]}', '${body["store_description"]}')
SQL;
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        
        // TODO: return the object
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        [ 'data' => [] ]
        ];

    }


    /* end PostStore */
}
