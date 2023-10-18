<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"staff"},
 *      path="/staff/membership/{id}",
 *      summary="Gets staff position",
 *      @OA\Parameter(
 *          description="Id of the staff position.",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Member staff positions found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/staff_entry"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/staff_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Modules\staff\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\PermissionDeniedException;
use App\Controller\NotFoundException;
use Atlas\Query\Select;

class GetStaffMembership extends BaseStaff
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $select = Select::new($this->container->db);
        $select->columns(
            '"staff_entry" AS type'
        )->columns(
            'l.ListRecordID AS id',
            'l.AccountID AS member',
            'l.DepartmentID AS department'
        )->columns(
            'COALESCE(l.Note, "") AS note'
        )->columns(
            $select->subselect()->columns(
                'Name'
            )->from(
                'ConComPositions'
            )->where(
                'PositionID = l.PositionID'
            )->as(
                'position'
            )->getStatement()
        )->from(
            'ConComList AS l'
        )->where(
            'ListRecordID = ',
            $params['id']
        );

        $data = $select->fetchAll();
        if (empty($data)) {
            throw new NotFoundException('Staff record not found');
        }

        $user = $request->getAttribute('oauth2-token')['user_id'];
        if ($data[0]['id'] != $user &&
            !$this->container->RBAC::havePermission('api.get.staff')) {
            throw new PermissionDeniedException();
        }

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $data[0],
        ];

    }


    /* end GetStaffMembership */
}
