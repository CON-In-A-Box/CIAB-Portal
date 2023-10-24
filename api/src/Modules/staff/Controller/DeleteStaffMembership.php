<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Delete(
 *      tags={"staff"},
 *      path="/staff/membership/{id}",
 *      summary="Deletes a staff member.",
 *      @OA\Parameter(
 *          description="Id of the position",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Response(
 *          response=204,
 *          description="OK"
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/staff_not_found"
 *      ),
 *      security={
 *          {"ciab_auth": {}}
 *       }
 *  )
 **/

namespace App\Modules\staff\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\NotFoundException;
use Atlas\Query\Delete;
use Atlas\Query\Select;

class DeleteStaffMembership extends BaseStaff
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $select = Select::new($this->container->db);
        $select->columns(
            'DepartmentID'
        )->from(
            'ConComList'
        )->where(
            'ListRecordID =',
            $params['id']
        );

        $record = $select->fetchAll();
        if (empty($record)) {
            throw new NotFoundException('Staff Membership  Record Not Found');
        }
        $target = $record[0]['DepartmentID'];

        $permissions = ['api.delete.staff.all',
        "api.delete.staff.$target"];
        $this->checkPermissions($permissions);

        $delete = Delete::new($this->container->db);
        $delete->from(
            'ConComList'
        )->where(
            'ListRecordID = ',
            $params['id']
        )->perform();
        $this->container->RBAC->reload();
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        [null],
        204
        ];

    }


    /* end DeleteStaffMembership */
}
