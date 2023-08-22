<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"staff"},
 *      path="/staff/positions",
 *      summary="List all staff position types",
 *      @OA\Response(
 *          response=200,
 *          description="OK",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/staff_position_list"
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/


namespace App\Modules\staff\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;

class ListStaffPositions extends BaseStaff
{

  
    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.get.staff'];
        $this->checkPermissions($permissions);
        $data = Select::new($this->container->db)
          ->columns('PositionID as id')
          ->columns('Name as position')
          ->from('ConComPositions')
          ->fetchAll();

        return [
          \App\Controller\BaseController::LIST_TYPE,
          $data,
          array('type' => 'staff_position_list')
        ];

    }


  /* end ListStaffPositions */
}
