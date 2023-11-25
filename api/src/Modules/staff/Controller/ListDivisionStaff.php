<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"departments"},
 *      path="/division/{id}/staff",
 *      summary="List staff for a division",
 *      @OA\Parameter(
 *          description="Division being listed",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
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

use Exception;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class PermissionDeniedException extends Exception
{
}

class ListDivisionStaff
{

    protected $rbac;

    protected $eventService;

    protected $departmentService;


    public function __construct(Container $container)
    {
        $this->rbac = $container->get('RBAC');
        $this->eventService = $container->get('event_service');
        $this->departmentService = $container->get('department_service');

    }


    public function __invoke(Request $request, Response $response, $args)
    {
        if ($this->rbac->havePermission('api.get.staff')) {
            $currentEvent = $this->eventService->getCurrentEvent();
            $data = $this->departmentService->getDivisionStaff($currentEvent['id'], $args['id']);
            return $response->withJson($data, 200);
        } else {
            return $response->withStatus(403);
        }

    }


    /* End ListDivisionStaff */
}
