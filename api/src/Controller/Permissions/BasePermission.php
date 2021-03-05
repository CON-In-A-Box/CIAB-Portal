<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Permissions;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Container;
use App\Controller\BaseController;
use App\Controller\NotFoundException;

abstract class BasePermission extends BaseController
{

    protected /*. array .*/ $methods;

    protected /*. string .*/ $restype;


    public function __construct(Container $container, string $restype, array $methods)
    {
        parent::__construct('permission', $container);
        $this->methods = $methods;
        $this->restype = $restype;

    }


    protected function buildDeptEntry($id, $allowed, $subtype, $method, $hateoas) : array
    {
        $entry = [
        'type' => 'permission_entry',
        'subtype' => $subtype.'_'.$method,
        'allowed' => $allowed,
        'action' => $hateoas
        ];
        $entry['subdata'] = [
        'departmentId' => $id
        ];
        return $entry;

    }


    protected function baseMethodResource(Request $request, Response $response, array $params): array
    {
        global $Departments;

        $methodArg = null;
        if (array_key_exists('method', $params)) {
            $methodArg = $params['method'];
        }
        if ($methodArg !== null) {
            if (!in_array($methodArg, $this->methods)) {
                throw new NotFoundException("Method '{$this->restype}.$methodArg' Invalid");
            }
        }
        $path = $request->getUri()->getBaseUrl();
        if (array_key_exists('department', $params)) {
            $data = $this->getDepartment($params['department']);
            $allowed = (\ciab\RBAC::havePermission("api.$methodArg.{$this->restype}.${data['id']}") ||
                        \ciab\RBAC::havePermission("api.$methodArg.{$this->restype}.all"));
            ;
            $result = $this->buildDeptEntry(
                $data['id'],
                $allowed,
                $this->restype,
                $methodArg,
                [
                'method' => $methodArg,
                'href' => "$path/{$this->restype}/".$data['id'],
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
                $methods = $this->methods;
            }
            foreach ($methods as $method) {
                foreach ($Departments as $key => $data) {
                    $allowed = (\ciab\RBAC::havePermission("api.$method.{$this->restype}.${data['id']}") ||
                                \ciab\RBAC::havePermission("api.$method.{$this->restype}.all"));
                    ;
                    if ($allowed) {
                        $result = $this->buildDeptEntry(
                            $data['id'],
                            $allowed,
                            $this->restype,
                            $method,
                            [
                            'method' => $method,
                            'href' => "$path/{$this->restype}/".$data['id'],
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


    protected function buildEntry($request, $id, $method, $restype): array
    {
        $path = $request->getUri()->getBaseUrl();
        $allowed = (\ciab\RBAC::havePermission("api.$method.$restype.$id") ||
                    \ciab\RBAC::havePermission("api.$method.$restype.all"));
        ;
        return $this->buildDeptEntry(
            $id,
            $allowed,
            $restype,
            $method,
            [
            'method' => $method,
            'href' => "$path/$restype/$id",
            'request' => strtoupper($method)
            ]
        );

    }


    protected function buildBaseResource(Request $request, Response $response, $params): array
    {
        global $Departments;

        $path = $request->getUri()->getBaseUrl();
        $result = array();
        $data = $this->getDepartment($params['department']);
        $id = $data['id'];
        $method = $params['method'];
        if ($method !== null && !in_array($method, $this->methods)) {
            throw new NotFoundException("Method '{$this->restype}.$method' Invalid");
        }
        if ($method !== null) {
            $result[] = $this->buildEntry($request, $id, $method, $this->restype);
        } else {
            foreach ($this->methods as $method) {
                $result[] = $this->buildEntry($request, $id, $method, $this->restype);
            }
        }
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $result,
        array('type' => 'permission_list')];

    }


    public function processIncludes(Request $request, Response $response, $args, $values, &$data)
    {
        if (in_array('departmentId', $values)) {
            $target = new \App\Controller\Department\GetDepartment($this->container);
            $newargs = $args;
            $newargs['name'] = $data['subdata']['departmentId'];
            $newdata = $target->buildResource($request, $response, $newargs)[1];
            if ($newdata['id'] != $data['id']) {
                $target->processIncludes($request, $response, $args, $values, $newdata);
                $data['subdata']['departmentId'] = $target->arrayResponse($request, $response, $newdata);
            }
        }

    }


    /* End BasePermission */
}
