<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Permissions;

use Slim\Http\Request;
use Slim\Http\Response;

class GenericResource extends GenericPermission
{


    private function buildEntry($request, $method, $resourceName, $detail): array
    {
        $path = $request->getUri()->getBaseUrl();
        $permission = 'api.'.$method.'.'.$resourceName.$detail;
        $allowed = (\ciab\RBAC::havePermission($permissions));
        return $this->buildBaseEntry(
            $allowed,
            $resourceName.$detail,
            $method,
            [
            'method' => $method,
            'href' => $path.'/'.$resourceName.'/'.$permission,
            'request' => strtoupper($method)
            ]
        );

    }


    public function buildResource(Request $request, Response $response, $args): array
    {
        global $Departments;

        $path = $request->getUri()->getBaseUrl();
        $result = array();
        $method = $args['method'];
        $resourceName = $args['resource'];
        $detail = $args['detail'];
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
        $result[] = $this->buildEntry($request, $method, $resourceName, '.'.$detail);
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $result,
        array('type' => 'permission_list')];

    }


    /* end GenericResource */
}
