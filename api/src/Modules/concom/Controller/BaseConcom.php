<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\concom\Controller;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\BaseController;

abstract class BaseConcom extends BaseController
{


    public function __construct(Container $container)
    {
        parent::__construct('concom', $container);

    }


    protected function getConComPosition($account, $event = null)
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
                'type' => 'concom_entry',
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


    public function processIncludes(Request $request, Response $response, $args, $values, &$data)
    {
        if (in_array('memberId', $values)) {
            $target = new \App\Controller\Member\GetMember($this->container);
            $newargs = $args;
            $newargs['name'] = $data['memberId'];
            $newdata = $target->buildResource($request, $response, $newargs);
            if ($newdata[0] == \App\Controller\BaseController::RESOURCE_TYPE) {
                $newdata = $newdata[1];
                $target->processIncludes($request, $response, $args, $values, $newdata);
                $data['memberId'] = $target->arrayResponse($request, $response, $newdata);
            }
        }
        if (in_array('departmentId', $values)) {
            $target = new \App\Controller\Department\GetDepartment($this->container);
            $newargs = $args;
            $newargs['name'] = $data['departmentId'];
            $newdata = $target->buildResource($request, $response, $newargs);
            if ($newdata[0] == \App\Controller\BaseController::RESOURCE_TYPE) {
                $newdata = $newdata[1];
                $target->processIncludes($request, $response, $args, $values, $newdata);
                $data['departmentId'] = $target->arrayResponse($request, $response, $newdata);
            }
        }

    }


    /* End BaseConcom */
}
