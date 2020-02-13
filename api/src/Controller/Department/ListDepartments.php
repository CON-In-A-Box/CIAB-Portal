<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Department;

require_once __DIR__.'/../../../../functions/divisional.inc';

use Slim\Http\Request;
use Slim\Http\Response;

class ListDepartments extends BaseDepartment
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        global $Departments;
        $output = array();
        foreach ($Departments as $key => $data) {
            $output[] = [
            'type' => 'department_entry',
            'id' => $data['id'],
            'get' => $this->buildDepartmentGet($request, $data['id'])
            ];
        }
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $output,
        array('type' => 'department_list')];

    }


    public function processIncludes(Request $request, Response $response, $args, $values, &$data)
    {
        if (in_array('id', $values)) {
            $target = new GetDepartment($this->container);
            $newargs = $args;
            $newargs['name'] = $data['id'];
            $newdata = $target->buildResource($request, $response, $newargs)[1];
            $target->processIncludes($request, $response, $args, $values, $newdata);
            $data['id'] = $target->arrayResponse($request, $response, $newdata);
        }

    }


    /* end ListDepartments */
}
