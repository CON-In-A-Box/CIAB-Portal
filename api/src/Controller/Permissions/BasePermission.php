<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Tag(
 *      name="permissions",
 *      description="Querying and checking permissions in the API"
 *  )
 *
 *  @OA\Schema(
 *      schema="permission_entry",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"permission_entry"}
 *      ),
 *      @OA\Property(
 *          property="subtype",
 *          type="string",
 *          description="Description string about the permission"
 *      ),
 *      @OA\Property(
 *          property="allowed",
 *          type="boolean",
 *          description="Is the permission enabled"
 *      ),
 *      @OA\Property(
 *          property="action",
 *          ref="#/components/schemas/permission_action"
 *      ),
 *      @OA\Property(
 *          property="subdata",
 *          ref="#/components/schemas/permission_subdata"
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="permission_subdata",
 *      @OA\Property(
 *          property="department",
 *          type="integer",
 *          description="Id for the target department"
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="permission_action",
 *      @OA\Property(
 *          property="method",
 *          type="string",
 *          description="Name of the method"
 *      ),
 *      @OA\Property(
 *          property="href",
 *          type="string",
 *          format="uri",
 *          description="URI for the method"
 *      ),
 *      @OA\Property(
 *          property="request",
 *          type="string",
 *          description="HTTP request type for the method"
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="permission_list",
 *      allOf = {
 *          @OA\Schema(ref="#/components/schemas/resource_list")
 *      },
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"permission_list"}
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="List of permissions",
 *          @OA\Items(
 *              ref="#/components/schemas/permission_entry"
 *          )
 *      )
 *  )
 *
 *  @OA\Response(
 *      response="permission_not_found",
 *      description="Permission not found in the system.",
 *      @OA\JsonContent(
 *          ref="#/components/schemas/error"
 *      )
 *   )
 *
 *  @OA\Get(
 *      tags={"permissions"},
 *      path="/permissions/method/{resource}",
 *      summary="Gets permissions on a resource by method",
 *      @OA\Parameter(
 *          description="Resource being queried.",
 *          in="path",
 *          name="resource",
 *          required=true,
 *          @OA\Schema(
 *              type="string",
 *              enum={"announcement","deadline"}
 *          )
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/max_results",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/page_token",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Permissions found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/permission_list"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/permission_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 *
 *  @OA\Get(
 *      tags={"permissions"},
 *      path="/permissions/method/{resource}/{method}",
 *      summary="Gets a method permission",
 *      @OA\Parameter(
 *          description="Resource being queried.",
 *          in="path",
 *          name="resource",
 *          required=true,
 *          @OA\Schema(
 *              type="string",
 *              enum={"announcement","deadline"}
 *          )
 *      ),
 *      @OA\Parameter(
 *          description="Method being queried.",
 *          in="path",
 *          name="method",
 *          required=true,
 *          @OA\Schema(
 *              type="string",
 *              enum={"get","put","post","delete"}
 *          )
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/max_results",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/page_token",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Permissions found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/permission_list"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/permission_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 *
 *  @OA\Get(
 *      tags={"permissions"},
 *      path="/permissions/method/{resource}/{method}/{department}",
 *      summary="Gets a method permission",
 *      @OA\Parameter(
 *          description="Resource being queried.",
 *          in="path",
 *          name="resource",
 *          required=true,
 *          @OA\Schema(
 *              type="string",
 *              enum={"announcement","deadline"}
 *          )
 *      ),
 *      @OA\Parameter(
 *          description="Method being queried.",
 *          in="path",
 *          name="method",
 *          required=true,
 *          @OA\Schema(
 *              type="string",
 *              enum={"get","put","post","delete"}
 *          )
 *      ),
 *      @OA\Parameter(
 *          description="The Id or name of the department",
 *          in="path",
 *          name="department",
 *          required=true,
 *          @OA\Schema(
 *              oneOf = {
 *                  @OA\Schema(
 *                      description="Department id",
 *                      type="integer"
 *                  ),
 *                  @OA\Schema(
 *                      description="Department name",
 *                      type="string"
 *                  )
 *              }
 *          )
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/max_results",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/page_token",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Permissions found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/permission_list"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/permission_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 *
 *  @OA\Get(
 *      tags={"permissions"},
 *      path="/permissions/resource/{resource}/{department}",
 *      summary="Gets a resource permission",
 *      @OA\Parameter(
 *          description="Resource being queried.",
 *          in="path",
 *          name="resource",
 *          required=true,
 *          @OA\Schema(
 *              type="string",
 *              enum={"announcement","deadline"}
 *          )
 *      ),
 *      @OA\Parameter(
 *          description="The Id or name of the department",
 *          in="path",
 *          name="department",
 *          required=true,
 *          @OA\Schema(
 *              oneOf = {
 *                  @OA\Schema(
 *                      description="Department name",
 *                      type="integer"
 *                  ),
 *                  @OA\Schema(
 *                      description="Department id",
 *                      type="string"
 *                  )
 *              }
 *          )
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/max_results",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/page_token",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Permissions found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/permission_list"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/permission_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 *
 *  @OA\Get(
 *      tags={"permissions"},
 *      path="/permissions/resource/{resource}/{department}/{method}",
 *      summary="Gets a resource permission",
 *      @OA\Parameter(
 *          description="Resource being queried.",
 *          in="path",
 *          name="resource",
 *          required=true,
 *          @OA\Schema(
 *              type="string",
 *              enum={"announcement","deadline"}
 *          )
 *      ),
 *      @OA\Parameter(
 *          description="The Id or name of the department",
 *          in="path",
 *          name="department",
 *          required=true,
 *          @OA\Schema(
 *              oneOf = {
 *                  @OA\Schema(
 *                      description="Department name",
 *                      type="integer"
 *                  ),
 *                  @OA\Schema(
 *                      description="Department id",
 *                      type="string"
 *                  )
 *              }
 *          )
 *      ),
 *      @OA\Parameter(
 *          description="Method being queried.",
 *          in="path",
 *          name="method",
 *          required=true,
 *          @OA\Schema(
 *              type="string",
 *              enum={"get","put","post","delete"}
 *          )
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/max_results",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/page_token",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Permissions found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/permission_list"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/permission_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 *
 *  @OA\Get(
 *      tags={"permissions"},
 *      path="/permissions/generic/{resource}/{method}",
 *      summary="Gets a generic permission",
 *      @OA\Parameter(
 *          description="Resource being queried.",
 *          in="path",
 *          name="resource",
 *          required=true,
 *          @OA\Schema(
 *              type="string",
 *          )
 *      ),
 *      @OA\Parameter(
 *          description="Method being queried.",
 *          in="path",
 *          name="method",
 *          required=true,
 *          @OA\Schema(
 *              type="string",
 *              enum={"get","put","post","delete"}
 *          )
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/max_results",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/page_token",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Permissions found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/permission_list"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/permission_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 *
 *  @OA\Get(
 *      tags={"permissions"},
 *      path="/permissions/generic/{resource}/{method}/{detail}",
 *      summary="Gets a generic permission",
 *      @OA\Parameter(
 *          description="Resource being queried.",
 *          in="path",
 *          name="resource",
 *          required=true,
 *          @OA\Schema(
 *              type="string",
 *          )
 *      ),
 *      @OA\Parameter(
 *          description="Method being queried.",
 *          in="path",
 *          name="method",
 *          required=true,
 *          @OA\Schema(
 *              type="string",
 *              enum={"get","put","post","delete"}
 *          )
 *      ),
 *      @OA\Parameter(
 *          description="detail of the method.",
 *          in="path",
 *          name="detail",
 *          required=true,
 *          @OA\Schema(
 *              type="string",
 *          )
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/max_results",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/page_token",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Permissions found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/permission_list"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/permission_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/


namespace App\Controller\Permissions;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Container;
use App\Controller\BaseController;
use App\Error\NotFoundException;

require_once __DIR__.'/../../../../functions/divisional.inc';

abstract class BasePermission extends BaseController
{

    protected /*. array .*/ $methods;

    protected /*. string .*/ $restype;


    public function __construct(Container $container, string $restype, array $methods = ['get', 'put', 'post', 'delete'])
    {
        parent::__construct('permission', $container);
        $this->methods = $methods;
        $this->restype = $restype;

    }


    public static function install($container): void
    {
        $container->RBAC->customizeRBAC('\App\Controller\Announcement\BaseAnnouncement\customizeAnnouncementRBAC');
        $container->RBAC->customizeRBAC('\App\Controller\Deadline\BaseDeadline::customizeDeadlineRBAC');

    }


    public static function permissions($database): ?array
    {
        return null;

    }


    protected function buildDeptEntry($id, $allowed, $subtype, $method, $link) : array
    {
        $entry = [
        'type' => 'permission_entry',
        'subtype' => $subtype.'_'.$method,
        'allowed' => ($allowed) ? 1 : 0,
        'action' => $link
        ];
        $entry['subdata'] = [
        'department' => $id
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
            $allowed = ($this->container->RBAC->havePermission("api.$methodArg.{$this->restype}.${data['id']}") ||
                        $this->container->RBAC->havePermission("api.$methodArg.{$this->restype}.all")) ? 1 : 0;
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
                    $allowed = ($this->container->RBAC->havePermission("api.$method.{$this->restype}.${data['id']}") ||
                                $this->container->RBAC->havePermission("api.$method.{$this->restype}.all")) ? 1 : 0;
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
        $allowed = ($this->container->RBAC->havePermission("api.$method.$restype.$id") ||
                    $this->container->RBAC->havePermission("api.$method.$restype.all")) ? 1 : 0;
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


    public function processIncludes(Request $request, Response $response, $params, &$data, $history = [])
    {
        parent::processIncludes($request, $response, $params, $data, $history);

        $target = new \App\Controller\Department\GetDepartment($this->container);
        $newparams = $params;
        $newparams['name'] = $data['subdata']['department'];
        $newdata = $target->buildResource($request, $response, $newparams)[1];
        if ($newdata['id'] != $data['subdata']['department']) {
            $target->processIncludes($request, $response, $params, $newdata, $history);
            $data['subdata']['department'] = $target->arrayResponse($request, $response, $newdata);
        }

    }


    /* End BasePermission */
}
