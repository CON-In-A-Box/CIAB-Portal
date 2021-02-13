<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Department;

use Slim\Http\Request;
use Slim\Http\Response;

class ListDeadlines extends \App\Controller\Deadline\BaseDeadline
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $department = $this->getDepartment($args['name']);
        if ($department === null) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse(
                $request,
                $response,
                'Not Found',
                'Department \''.$args['name'].'\' Not Found',
                404
            )];
        }

        $permissions = ['api.get.deadline.all',
        'api.get.deadline.'.$department['id']];
        $this->checkPermissions($permissions);

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
