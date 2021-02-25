<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Department;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\NotFoundException;

class ListDeadlines extends \App\Controller\Deadline\BaseDeadline
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $department = $this->getDepartment($params['name']);
        if ($department === null) {
            throw new NotFoundException('Department \''.$params['name'].'\' Not Found');
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


    public function processIncludes(Request $request, Response $response, $params, $values, &$data)
    {
        if (in_array('departmentId', $values)) {
            $target = new \App\Controller\Department\GetDepartment($this->container);
            $newargs = $params;
            $newargs['name'] = $data['departmentId'];
            $newdata = $target->buildResource($request, $response, $newargs)[1];
            $target->processIncludes($request, $response, $params, $values, $newdata);
            $data['departmentId'] = $target->arrayResponse($request, $response, $newdata);
        }

    }


    /* end ListDeadlines */
}
