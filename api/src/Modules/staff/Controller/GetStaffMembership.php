<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"staff"},
 *      path="/staff_membership/{id}",
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

class GetStaffMembership extends BaseStaff
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $id = $params['id'];
        $sql = "SELECT * FROM `ConComList` WHERE `ListRecordID` = $id";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll();

        if (empty($data)) {
            throw new NotFoundException('Staff record not found');
        }

        $user = $request->getAttribute('oauth2-token')['user_id'];
        if ($data[0]['AccountID'] != $user &&
            !\ciab\RBAC::havePermission('api.get.staff')) {
            throw new PermissionDeniedException();
        }

        $sql = <<<SQL
            SELECT
                *,
                (
                    SELECT
                        Name
                    FROM
                        Departments
                    WHERE
                        DepartmentID = c.DepartmentID
                ) as Department,
                (
                    SELECT
                        Name
                    FROM
                        ConComPositions
                    WHERE
                        PositionID = c.PositionID
                ) as Position
            FROM
                ConComList as c
            WHERE
                ListRecordID = $id;
SQL;
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $sth->fetch();

        $path = $request->getUri()->getBaseUrl();
        $entry = $this->buildEntry($request, $data['ListRecordID'], $data['DepartmentID'], $user, $data['Note'], $data['Position']);

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $entry,
        ];

    }


    /* end GetStaffMembership */
}
