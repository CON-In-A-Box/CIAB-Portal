<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Department;

use Slim\Http\Request;
use Slim\Http\Response;

class GetDepartment extends BaseDepartment
{


    public function __invoke(Request $request, Response $response, $args)
    {
        $output = $this->getDepartment($args['name']);
        if ($output) {
            $email = [];
            foreach ($output['Email'] as $entry) {
                $alias = boolval($entry['IsAlias']);
                $email[] = [
                'email' => $entry['EMail'],
                'isAlias' => $alias
                ];
            }
            $output['name'] = $output['Name'];
            $output['division'] = $output['Division'];
            $output['fallback'] = $output['FallbackID'];
            $output['email'] = $email;
            unset($output['Email']);
            unset($output['Name']);
            unset($output['Division']);
            unset($output['Fallback']);
            unset($output['FallbackID']);
            $this->buildDepartmentHateoas($request);
            return $this->jsonResponse($request, $response, $output);
        } else {
            return $this->errorResponse(
                $request,
                $response,
                'Not Found',
                'Department \''.$args['name'].'\' Not Found',
                404
            );
        }

    }


    /* end GetDepartment */
}
