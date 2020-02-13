<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Permissions;

use Slim\Http\Request;
use Slim\Http\Response;

class DeadlineMethod extends DeadlinePermission
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        global $Departments;


        $methodArg = null;
        if (array_key_exists('method', $args)) {
            $methodArg = $args['method'];
        }
        if ($methodArg !== null) {
            if (!in_array($methodArg, DeadlinePermission::ALL_METHODS)) {
                return [
                \App\Controller\BaseController::RESULT_TYPE,
                $this->errorResponse(
                    $request,
                    $response,
                    'Not Found',
                    'Method \'deadline.'.$methodArg.'\' Invalid',
                    404
                )];
            }
        }
        $path = $request->getUri()->getBaseUrl();
        if (array_key_exists('department', $args)) {
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
            $allowed = (\ciab\RBAC::havePermission('api.'.$methodArg.'.deadline.'.$data['id']) ||
                        \ciab\RBAC::havePermission('api.'.$methodArg.'.deadline.all'));
            ;
            $result = $this->buildDeptEntry(
                $data['id'],
                $allowed,
                'deadline',
                $methodArg,
                [
                'method' => $methodArg,
                'href' => $path.'/deadline/'.$data['id'],
                'request' => strtoupper($methodArg)
                ]
            );
            return [
            \App\Controller\BaseController::RESOURCE_TYPE,
            $result];
        } else {
            $output = array();
            if ($methodArg !== null) {
                $methods = [$methodArg];
            } else {
                $methods = DeadlinePermission::ALL_METHODS;
            }
            foreach ($methods as $method) {
                foreach ($Departments as $key => $data) {
                    $allowed = (\ciab\RBAC::havePermission('api.'.$method.'.deadline.'.$data['id']) ||
                                \ciab\RBAC::havePermission('api.'.$method.'.deadline.all'));
                    ;
                    if ($allowed) {
                        $result = $this->buildDeptEntry(
                            $data['id'],
                            $allowed,
                            'deadline',
                            $method,
                            [
                            'method' => $method,
                            'href' => $path.'/deadline/'.$data['id'],
                            'request' => strtoupper($method)
                            ]
                        );
                        $output[] = $result;
                    }
                }
            }
            return [
            \App\Controller\BaseController::LIST_TYPE,
            $output,
            array('type' => 'permission_list')];
        }

    }


    /* end DeadlineMethod */
}
