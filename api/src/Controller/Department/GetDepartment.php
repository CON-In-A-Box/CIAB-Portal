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

class GetDepartment extends BaseDepartment
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $output = $this->getDepartment($params['name']);
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $output];

    }


    /* end GetDepartment */
}
