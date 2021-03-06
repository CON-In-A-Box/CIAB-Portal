<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Department;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Container;
use App\Controller\NotFoundException;
use App\Controller\IncludeResource;

class GetDepartment extends BaseDepartment
{


    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->includes = [
        new IncludeResource('\App\Controller\Department\GetDepartment', 'name', 'fallback')
        ];

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $output = $this->getDepartment($params['name']);
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

    }


    public function processIncludes(Request $request, Response $response, $params, $values, &$data)
    {
        parent::processIncludes($request, $response, $params, $values, $data);
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

    }


    /* end GetDepartment */
}
