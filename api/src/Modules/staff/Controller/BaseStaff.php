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
 *  @OA\Schema(
 *      schema="staff_position_entry",
 *      @OA\Property(
 *          property="id",
 *          type="integer"
 *      ),
 *      @OA\Property(
 *          property="position",
 *          type="string"
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="staff_position_list",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"staff_position_list"}
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="List of department position types",
 *          @OA\Items(
 *              ref="#/components/schemas/staff_position_entry"
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
use Atlas\Query\Select;

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


    protected function selectStaff($event, $department = null, $member = null)
    {
        $select = Select::new($this->container->db);

        $historical = $select->subselect()->columns(
            'DepartmentID'
        )->from(
            'Departments'
        )->where(
            "Name = 'Historical Placeholder'"
        );
        $historicalParents = $select->subselect()->columns(
            'DepartmentID'
        )->from(
            'Departments'
        )->where(
            'ParentDepartmentID IN ',
            $historical
        );

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
            'l.EventID = ',
            $event
        );

        if ($department !== null) {
            $department = $this->getDepartment($department);
            $select->where('l.DepartmentID = ', $department['id']);
        }

        if ($member !== null) {
            $select->where('l.AccountID = ', $member);
        }

        $select->where(
            'l.DepartmentID NOT IN ',
            $historical
        )->where(
            'l.DepartmentID NOT IN ',
            $historicalParents
        );
        return $select->fetchAll();

    }


    /* End BaseStaff */
}
