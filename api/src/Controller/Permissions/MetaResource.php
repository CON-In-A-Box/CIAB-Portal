<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Permissions;

use Slim\Http\Request;
use Slim\Http\Response;

class MetaResource extends GenericPermission
{


    protected function metaBuildEntry($request, $resourceName): array
    {
        $path = $request->getUri()->getBaseUrl();
        $permission = $resourceName;
        $allowed = ($this->container->RBAC->havePermission($permission)) ? 1 : 0;

        $entry = [
        'type' => 'permission_entry',
        'subtype' => $permission,
        'allowed' => $allowed,
        'action' => null,
        'subdata' => null
        ];
        return $entry;

    }


    public function buildResource(Request $request, Response $response, $args): array
    {
        $path = $request->getUri()->getBaseUrl();
        $result = $this->metaBuildEntry($request, $args['permission']);
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $result
        ];

    }


    /* end MetaResource */
}
