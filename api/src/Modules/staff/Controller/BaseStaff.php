<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

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
            'memberId'
        ),
        new IncludeResource(
            '\App\Controller\Department\GetDepartment',
            'name',
            'departmentId'
        )
        ];

    }


    protected function getStaffPosition($account, $event = null)
    {
        if ($event == null) {
            $event = \current_eventID();
            if ($event === null) {
                return array();
            }
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
        $path = $request->getUri()->getBaseUrl();
        return ([
                'type' => 'staff_entry',
                'id' => $id,
                'memberId' => $member,
                'note' => $note,
                'position' => $position,
                'departmentId' => $dept,
                'links' => array([
                    'method' => 'member',
                    'href' => $path.'/member/'.$member,
                    'request' => 'GET'
                    ],
                    [
                    'method' => 'department',
                    'href' => $path.'/department/'.$dept,
                    'request' => 'GET'
                    ]
            )
        ]);

    }


    /* End BaseStaff */
}
