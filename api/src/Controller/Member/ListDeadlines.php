<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Member;

use Slim\Http\Request;
use Slim\Http\Response;

class ListDeadlines extends BaseMember
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        if (array_key_exists('name', $args)) {
            $data = $this->findMember($request, $response, $args, 'name');
            if (gettype($data) === 'object') {
                return $data;
            }
            $user = $data['Id'];
        } else {
            $user = $request->getAttribute('oauth2-token')['user_id'];
        }
        $sth = $this->container->db->prepare(<<<SQL
            SELECT
                *
            FROM
                `Deadlines`
            WHERE
                `DepartmentID` IN(
                SELECT
                    `DepartmentID`
                FROM
                    `ConComList`
                WHERE
                    `AccountID` = '$user'
            ) OR `DepartmentID` IN(
                SELECT
                    `DepartmentID`
                FROM
                    `Departments`
                WHERE
                    `ParentDepartmentID` IN(
                    SELECT
                        `DepartmentID`
                    FROM
                        `ConComList`
                    WHERE
                        `AccountID` = '$user'
                )
            )
            ORDER BY `Deadline` ASC
SQL
        );
        $sth->execute();
        $todos = $sth->fetchAll();
        $output = array();
        $output['type'] = 'deadline_list';
        $data = array();
        foreach ($todos as $entry) {
            $deadline = new \App\Controller\Deadline\GetDeadline($this->container);
            $result = $deadline->buildDeadline($request, $response, $entry['DeadlineID'], $entry['DepartmentID'], $entry['Deadline'], $entry['Note']);
            $data[] = $deadline->arrayResponse($request, $response, $result);
        }
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        $output];

    }


    public function processIncludes(Request $request, Response $response, $args, $values, &$data)
    {
        if (in_array('departmentId', $values)) {
            $target = new \App\Controller\Department\GetDepartment($this->container);
            $newargs = $args;
            $newargs['name'] = $data['departmentId'];
            $newdata = $target->buildResource($request, $response, $newargs)[1];
            $target->processIncludes($request, $response, $args, $values, $newdata);
            $data['departmentId'] = $target->arrayResponse($request, $response, $newdata);
        }

    }


    /* end ListDeadlines */
}
