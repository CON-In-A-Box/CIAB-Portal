<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"departments"},
 *      path="/department/{id}",
 *      summary="Gets a department",
 *      @OA\Parameter(
 *          description="Id or name of the deadline",
 *          in="path",
 *          name="id",
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
 *                  enum={"fallback","division"}
 *              )
 *           )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Department found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/department"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/department_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Controller\Department;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Container;
use App\Controller\NotFoundException;
use App\Controller\IncludeResource;

class GetDepartment extends BaseDepartment
{


    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->includes = [
        new IncludeResource('\App\Controller\Department\GetDepartment', 'name', 'fallback')
        ];

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $output = $this->getDepartment($params['name']);
        $email = [];
        foreach ($output['Email'] as $entry) {
            $alias = boolval($entry['IsAlias']);
            $email[] = [
            'email' => $entry['EMail'],
            'isAlias' => $alias
            ];
        }
        $output['name'] = $output['Name'];
        $output['division'] = $output['Division'];
        $output['fallback'] = $output['FallbackID'];
        $output['email'] = $email;
        unset($output['Email']);
        unset($output['Name']);
        unset($output['Division']);
        unset($output['Fallback']);
        unset($output['FallbackID']);
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $output];

    }


    public function processIncludes(Request $request, Response $response, $params, $values, &$data)
    {
        parent::processIncludes($request, $response, $params, $values, $data);
        if (in_array('division', $values)) {
            $target = new GetDepartment($this->container);
            $newargs = $params;
            $newargs['name'] = $data['division'];
            $newdata = $target->buildResource($request, $response, $newargs)[1];
            if ($newdata['id'] != $data['id']) {
                $target->processIncludes($request, $response, $params, $values, $newdata);
                $data['division'] = $target->arrayResponse($request, $response, $newdata);
            }
        }

    }


    /* end GetDepartment */
}
