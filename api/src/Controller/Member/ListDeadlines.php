<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Member;

use Slim\Http\Request;
use Slim\Http\Response;

class ListDeadlines extends BaseMember
{


    public function __invoke(Request $request, Response $response, $args)
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
        return $this->listResponse($request, $response, $output, $data);

    }


    /* end ListDeadlines */
}
