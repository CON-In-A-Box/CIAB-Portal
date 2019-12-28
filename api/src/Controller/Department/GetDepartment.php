<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Department;

require_once __DIR__.'/../../../../functions/divisional.inc';

use Slim\Http\Request;
use Slim\Http\Response;

class GetDepartment extends BaseDepartment
{


    public function __invoke(Request $request, Response $response, $args)
    {
        global $Departments;
        $id = $args['name'];
        $output = null;
        if (array_key_exists($id, $Departments)) {
            $output = array('Name' => $id);
            $output = array_merge($output, $Departments[$id]);
        } else {
            foreach ($Departments as $key => $dept) {
                if ($dept['id'] == $id) {
                    $output = array('Name' => $key);
                    $output = array_merge($output, $dept);
                    break;
                }
            }
        }
        if ($output) {
            $this->buildDepartmentHateoas($request);
            return $this->jsonResponse($request, $response, $output);
        } else {
            return $this->errorResponse(
                $request,
                $response,
                'Not Found',
                'Department \''.$id.'\' Not Found',
                404
            );
        }

    }


    /* end GetDepartment */
}
