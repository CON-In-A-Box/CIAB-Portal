<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Department;

require_once __DIR__.'/../../../../functions/divisional.inc';

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\IncludeResource;

class ListDepartments extends BaseDepartment
{


    public function __construct($container)
    {
        parent::__construct($container);
        $this->includes = [
        new IncludeResource('\App\Controller\Department\GetDepartment', 'name', 'id')
        ];

    }


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


    /* end ListDepartments */
}
