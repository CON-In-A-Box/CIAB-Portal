<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"departments"},
 *      path="/department/{id}/staff",
 *      summary="List staff for a department",
 *      @OA\Parameter(
 *          description="Department being listed",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Parameter(
 *          parameter="subdepartments",
 *          description="include sub-departments",
 *          in="query",
 *          name="subdepartments",
 *          required=false,
 *          style="form",
 *          @OA\Schema(
 *              type="integer",
 *              enum={0, 1}
 *          )
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/event",
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
 *          description="OK",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/staff_list"
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          description="Event or Department not found in the system.",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/error"
 *          )
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/


namespace App\Modules\staff\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

class ListDepartmentStaff extends BaseStaff
{


    public function buildResource(Request $request, Response $response, $params) :array
    {
        $permissions = ['api.get.staff'];
        $this->checkPermissions($permissions);
        $event = $this->getEventId($request);
        $sub = boolval($request->getQueryParam('subdepartments', 0));
        $data = $this->selectStaff($event, $params['id'], null, $sub);
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        array('type' => 'staff_list')
        ];

    }


    /* end ListDepartmentStaff */
}
