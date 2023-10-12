<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"departments"},
 *      path="/staff",
 *      summary="List staff all departments",
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

class ListStaff extends BaseStaff
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.get.staff'];
        $this->checkPermissions($permissions);
        $event = $this->getEventId($request);
        $data = $this->selectStaff($event);
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        array( 'type' => 'staff_list', 'event' => $event )
        ];

    }


    /* end ListStaff */
}
