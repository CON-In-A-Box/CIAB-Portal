<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Tag(
 *      name="staff",
 *      description="Features around event staff. (Only avalaible if the 'staff' module is enabled)"
 *  )
 *
 *  @OA\Schema(
 *      schema="staff_entry",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"staff_entry"}
 *      ),
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          description="Entry Id"
 *      ),
 *      @OA\Property(
 *          property="member",
 *          description="Member resource",
 *          oneOf={
 *              @OA\Schema(
 *                  ref="#/components/schemas/member"
 *              ),
 *              @OA\Schema(
 *                  type="integer",
 *                  description="Member Id"
 *              )
 *          }
 *      ),
 *      @OA\Property(
 *          property="note",
 *          type="string",
 *          description="Note about this staffing entry"
 *      ),
 *      @OA\Property(
 *          property="position",
 *          type="string",
 *          description="Title of the staff position."
 *      ),
 *      @OA\Property(
 *          property="departemnt",
 *          description="The department of the staff position.",
 *          oneOf={
 *              @OA\Schema(
 *                  ref="#/components/schemas/department"
 *              ),
 *              @OA\Schema(
 *                  type="integer",
 *                  description="departemnt Id"
 *              )
 *          }
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="staff_list",
 *      allOf = {
 *          @OA\Schema(ref="#/components/schemas/resource_list")
 *      },
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"staff_list"}
 *      ),
 *      @OA\Property(
 *          property="event",
 *          type="integer",
 *          description="Id of the event being listed."
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="List of staff positions",
 *          @OA\Items(
 *              ref="#/components/schemas/staff_entry"
 *          )
 *      )
 *  )
 *
 *   @OA\Response(
 *      response="staff_not_found",
 *      description="Staff Position not found in the system.",
 *      @OA\JsonContent(
 *          ref="#/components/schemas/error"
 *      )
 *   )
 **/

namespace App\Modules\staff\Controller;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\BaseController;
use App\Controller\IncludeResource;

abstract class BaseStaff extends BaseController
{


    public function __construct(Container $container)
    {
        parent::__construct('staff', $container);

        $this->includes = [
        new IncludeResource(
            '\App\Controller\Member\GetMember',
            'id',
            'member'
        ),
        new IncludeResource(
            '\App\Controller\Department\GetDepartment',
            'name',
            'department'
        )
        ];

    }


    protected function getStaffPosition($account, $event)
    {
        $event = $this->getEvent($event)['id'];
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
                AccountID = $account
                AND EventID = $event
                AND DepartmentID NOT IN (
                    SELECT
                        `DepartmentID`
                    FROM
                        `Departments`
                    WHERE
                        Name = 'Historical Placeholder'
                )
                AND DepartmentID NOT IN (
                    SELECT
                        `DepartmentID`
                    FROM
                        `Departments`
                    WHERE
                        ParentDepartmentID IN (
                            SELECT
                                `DepartmentID`
                            FROM
                                `Departments`
                            WHERE
                                Name = 'Historical Placeholder'
                        )
                )
SQL;
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        return $sth->fetchAll();

    }


    protected function buildEntry(Request $request, $id, $dept, $member, $note, $position)
    {
        return ([
                'type' => 'staff_entry',
                'id' => $id,
                'member' => $member,
                'note' => $note,
                'position' => $position,
                'department' => $dept
        ]);

    }


    /* End BaseStaff */
}
