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
 *          property="departmentId",
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
 *          description="Include the resource instead of the ID.",
 *          in="query",
 *          name="include",
 *          required=false,
 *          explode=false,
 *          style="form",
 *          @OA\Schema(
 *              type="array",
 *              @OA\Items(
 *                  type="string",
 *                  enum={"departmentId"}
 *              )
 *          )
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/maxResults",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/pageToken",
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
 *          description="Include the resource instead of the ID.",
 *          in="query",
 *          name="include",
 *          required=false,
 *          explode=false,
 *          style="form",
 *          @OA\Schema(
 *              type="array",
 *              @OA\Items(
 *                  type="string",
 *                  enum={"departmentId"}
 *              )
 *          )
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/maxResults",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/pageToken",
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
 *          description="Include the resource instead of the ID.",
 *          in="query",
 *          name="include",
 *          required=false,
 *          explode=false,
 *          style="form",
 *          @OA\Schema(
 *              type="array",
 *              @OA\Items(
 *                  type="string",
 *                  enum={"departmentId"}
 *              )
 *          )
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/maxResults",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/pageToken",
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
 *          description="Include the resource instead of the ID.",
 *          in="query",
 *          name="include",
 *          required=false,
 *          explode=false,
 *          style="form",
 *          @OA\Schema(
 *              type="array",
 *              @OA\Items(
 *                  type="string",
 *                  enum={"departmentId"}
 *              )
 *          )
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/maxResults",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/pageToken",
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
 *          description="Include the resource instead of the ID.",
 *          in="query",
 *          name="include",
 *          required=false,
 *          explode=false,
 *          style="form",
 *          @OA\Schema(
 *              type="array",
 *              @OA\Items(
 *                  type="string",
 *                  enum={"departmentId"}
 *              )
 *          )
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/maxResults",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/pageToken",
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


    protected function buildDeptEntry($id, $allowed, $subtype, $method, $link) : array
    {
        $entry = [
        'type' => 'permission_entry',
        'subtype' => $subtype.'_'.$method,
        'allowed' => $allowed,
        'action' => $link
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


    public function processIncludes(Request $request, Response $response, $params, $values, &$data)
    {
        parent::processIncludes($request, $response, $params, $values, $data);

        if (in_array('departmentId', $values)) {
            $target = new \App\Controller\Department\GetDepartment($this->container);
            $newparams = $params;
            $newparams['name'] = $data['subdata']['departmentId'];
            $newdata = $target->buildResource($request, $response, $newparams)[1];
            if ($newdata['id'] != $data['id']) {
                $target->processIncludes($request, $response, $params, $values, $newdata);
                $data['subdata']['departmentId'] = $target->arrayResponse($request, $response, $newdata);
            }
        }

    }


    /* End BasePermission */
}
