<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Permissions;

use Slim\Http\Request;
use Slim\Http\Response;

class GenericResource extends GenericPermission
{


    protected function buildEntry($request, $method, $resourceName, $detail): array
    {
        $path = $request->getUri()->getBaseUrl();
        $permission = 'api.'.$method.'.'.$resourceName;
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
        global $Departments;

        $path = $request->getUri()->getBaseUrl();
        $result = array();
        $method = $args['method'];
        $resourceName = $args['resource'];
        if (array_key_exists('detail', $args)) {
            $detail = $args['detail'];
        } else {
            $detail = null;
        }
        if (!in_array($method, GenericPermission::ALL_METHODS)) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse(
                $request,
                $response,
                'Not Found',
                'Method \''.$resourceName.'.'.$method.'\' Invalid',
                404
            )];
        }
        if ($detail) {
            $resourceName .= '.'.$detail;
        }
        $result[] = $this->buildEntry($request, $method, $resourceName, null);
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $result,
        array('type' => 'permission_list')];

    }


    /* end GenericResource */
}
