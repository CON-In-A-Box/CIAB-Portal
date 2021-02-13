<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Department;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\NotFoundException;

class GetDepartment extends BaseDepartment
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $output = $this->getDepartment($params['name']);
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
            return [
            \App\Controller\BaseController::RESOURCE_TYPE,
            $output];
        } else {
            throw new NotFoundException('Department \''.$params['name'].'\' Not Found');
        }

    }


    public function processIncludes(Request $request, Response $response, $params, $values, &$data)
    {
        if (in_array('division', $values)) {
            $target = new GetDepartment($this->container);
            $newargs = $params;
            $newargs['name'] = $data['division'];
            $newdata = $target->buildResource($request, $response, $newargs)[1];
            if ($newdata['id'] != $data['id']) {
                $target->processIncludes($request, $response, $params, $values, $newdata);
                $data['division'] = $target->arrayResponse($request, $response, $newdata);
            }
        }
        if (in_array('fallback', $values) &&
            $data['fallback'] != $data['id'] &&
            $data['fallback'] != null) {
            $target = new GetDepartment($this->container);
            $newargs = $params;
            $newargs['name'] = $data['fallback'];
            $newdata = $target->buildResource($request, $response, $newargs)[1];
            $target->processIncludes($request, $response, $params, $values, $newdata);
            $data['fallback'] = $target->arrayResponse($request, $response, $newdata);
        }

    }


    /* end GetDepartment */
}
