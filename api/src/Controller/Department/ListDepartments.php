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


    public function __invoke(Request $request, Response $response, $args)
    {
        global $Departments;
        $output = array();
        foreach ($Departments as $key => $data) {
            $output[] = [
            'type' => 'department_entry',
            'name' => $key,
            'id' => $data['id'],
            'division' => $data['Division'],
            'get' => $this->buildDepartmentGet($request, $data['id'])
            ];
        }
        return $this->listResponse($request, $response, array('type' => 'department_list'), $output);

    }


    /* end ListDepartments */
}
