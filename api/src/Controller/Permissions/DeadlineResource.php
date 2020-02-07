<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Permissions;

use Slim\Http\Request;
use Slim\Http\Response;

class DeadlineResource extends DeadlinePermission
{


    private function buildEntry($request, $id, $method): array
    {
        $path = $request->getUri()->getBaseUrl();
        $allowed = (\ciab\RBAC::havePermission('api.'.$method.'.deadline.'.$id) ||
                    \ciab\RBAC::havePermission('api.'.$method.'.deadline.all'));
        ;
        return $this->buildDeptEntry(
            $id,
            $allowed,
            'deadline',
            $method,
            [
            'method' => $method,
            'href' => $path.'/deadline/'.$id,
            'request' => strtoupper($method)
            ]
        );

    }


    public function buildResource(Request $request, Response $response, $args): array
    {
        global $Departments;

        $path = $request->getUri()->getBaseUrl();
        $result = array();
        $data = $this->getDepartment($args['department']);
        if ($data === null) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse(
                $request,
                $response,
                'Not Found',
                'Department \''.$args['department'].'\' Invalid',
                404
            )];
        }
        $id = $data['id'];
        $method = $args['method'];
        if ($method !== null && !in_array($method, DeadlinePermission::ALL_METHODS)) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse(
                $request,
                $response,
                'Not Found',
                'Method \'deadline.'.$method.'\' Invalid',
                404
            )];
        }
        if ($method !== null) {
            $result[] = $this->buildEntry($request, $id, $method);
        } else {
            foreach (DeadlinePermission::ALL_METHODS as $method) {
                $result[] = $this->buildEntry($request, $id, $method);
            }
        }
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $result,
        array('type' => 'permission_list')];

    }


    /* end DeadlineResource */
}
