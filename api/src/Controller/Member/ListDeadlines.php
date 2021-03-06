<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Member;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Container;
use App\Controller\IncludeResource;

class ListDeadlines extends BaseMember
{


    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->includes = [
        new IncludeResource('\App\Controller\Department\GetDepartment', 'name', 'departmentId')
        ];

    }


    public function buildResource(Request $request, Response $response, $args): array
    {
        $data = $this->findMemberId($request, $response, $args, 'id');
        $user = $data['id'];
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


    /* end ListDeadlines */
}
