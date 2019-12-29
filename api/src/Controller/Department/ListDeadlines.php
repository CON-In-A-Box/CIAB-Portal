<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Department;

use Slim\Http\Request;
use Slim\Http\Response;

class ListDeadlines extends \App\Controller\Deadline\BaseDeadline
{


    public function __invoke(Request $request, Response $response, $args)
    {
        $department = $this->getDepartment($args['name']);
        if ($department === null) {
            return $this->errorResponse(
                $request,
                $response,
                'Not Found',
                'Department \''.$args['name'].'\' Not Found',
                404
            );
        }
        if (\ciab\RBAC::havePermission('api.get.deadline.'.$department['id'])) {
            $sth = $this->container->db->prepare(
                "SELECT * FROM `Deadlines` WHERE DepartmentID = '".$department['id']."' ORDER BY `Deadline` ASC"
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
        } else {
            return $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403);
        }

    }


    /* end ListDeadlines */
}
