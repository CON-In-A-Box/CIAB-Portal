<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller;

use Slim\Container;
use Slim\Http\Response;
use Slim\Http\Request;

require_once __DIR__.'/../../../backends/RBAC.inc';
require_once __DIR__.'/../../../functions/divisional.inc';

abstract class BaseController
{

    public const LIST_TYPE = 'list';
    public const RESULT_TYPE = 'result';
    public const RESOURCE_TYPE = 'resource';

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string
     */
    protected $api_type;

    /**
     * @var array[]
    */
    protected $hateoas;

    /**
     * @var array[]
    */
    protected $chain;


    protected function __construct(string $api_type, Container $container)
    {
        $this->api_type = $api_type;
        $this->container = $container;
        $this->hateoas = [];
        $this->chain = [];

        $modules = $container->get('settings')['modules'];
        foreach ($modules as $module) {
            if (class_exists($module)) {
                $target = new $module($this);
                if ($target->valid()) {
                    $this->chain[] = $target;
                }
            }
        }

    }


    abstract public function buildResource(Request $request, Response $response, $args): array;


    public function processIncludes(Request $request, Response $response, $args, $includes, &$data)
    {

    }


    public function __invoke(Request $request, Response $response, $args)
    {
        $result = $this->buildResource($request, $response, $args);
        if ($result === null || $result[0] === null) {
            return null;
        }
        $type = $result[0];
        $data = $result[1];
        if ($type == BaseController::LIST_TYPE) {
            $includes = $request->getQueryParam('include', null);
            $output = $result[2];
            if ($includes) {
                $values = array_map('trim', explode(',', $includes));
                for ($i = 0; $i < count($data); $i++) {
                    $this->processIncludes($request, $response, $args, $values, $data[$i]);
                }
            }
            return $this->listResponse($request, $response, $output, $data);
        } elseif ($type == BaseController::RESULT_TYPE) {
            return $data;
        } else {
            $includes = $request->getQueryParam('include', null);
            if ($includes) {
                $values = array_map('trim', explode(',', $includes));
                $this->processIncludes($request, $response, $args, $values, $data);
            }
            return $this->jsonResponse($request, $response, $data);
        }

    }


    public function addHateoasLink(string $method, string $href, string $request)
    {
        $this->hateoas[] = [
        'method' => $method,
        'href' => $href,
        'request' => $request
        ];

    }


    protected function filterOutput(Request $request, $data, $code): array
    {
        if ($code == 200) {
            $param = $request->getQueryParam('fields', null);
            if ($param !== null) {
                $fields = array_map('trim', explode(',', $param));
                $fields[] = 'type';
                $fields[] = 'data';
                $result = array();
                foreach ($data as $key => $value) {
                    if (in_array($key, $fields)) {
                        $result[$key] = $value;
                    }
                    if ($key === 'data') {
                        $newdata = array();
                        foreach ($result['data'] as $entry) {
                            $newentry = array();
                            foreach ($entry as $subkey => $subvalue) {
                                if (in_array($subkey, $fields)) {
                                    $newentry[$subkey] = $subvalue;
                                }
                            }
                            $newdata[] = $newentry;
                        }
                        $result['data'] = $newdata;
                    }
                }
                return $result;
            }
        }
        return $data;

    }


    protected function jsonResponse(Request $request, Response $response, $data, $code = 200): Response
    {
        foreach ($this->chain as $child) {
            $data = $child->handle($request, $response, $data, $code);
        }

        $parameters = null;
        $value = $request->getQueryParam('pretty', false);
        if ($value) {
            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            if ($value) {
                $parameters = JSON_PRETTY_PRINT;
            }
        }
        if (!empty($data) && !array_key_exists('type', $data)) {
            $data['type'] = $this->api_type;
        }
        if (!empty($data) && !empty($this->hateoas) &&
            !array_key_exists('links', $data)) {
            $data['links'] = $this->hateoas;
        }
        $output = $this->filterOutput($request, $data, $code);
        return $response->withJson($output, $code, $parameters);

    }


    protected function errorResponse(Request $request, Response $response, string $status, $message, int $code):  Response
    {
        $result = [
        'type' => 'error',
        'code' => $code,
        'status' => $status,
        'message' => $message,
        ];
        return $this->jsonResponse($request, $response, $result, $code);

    }


    protected function listResponse(Request $request, Response $response, $output, $data, $code = 200): Response
    {
        $len = count($data);
        $index = intval($request->getQueryParam('pageToken', 0));
        $maxPage = $request->getQueryParam('maxResults', 100);
        $count = $maxPage;
        if (is_string($count) && strtolower($count) === 'all') {
            $count = $len;
        } else {
            $count = intval($count);
        }

        if ($output === null) {
            $output = array();
        }
        $output['data'] = array_slice($data, $index, $count, true);
        if ($maxPage !== 'all' and $index + $count < $len) {
            $output['nextPageToken'] = $index + $count;
        }

        return $this->jsonResponse($request, $response, $output, $code);

    }


    protected function arrayResponse(Request $request, Response $response, $data, $code = 200): Array
    {
        foreach ($this->chain as $child) {
            $data = $child->handle($request, $response, $data, $code);
        }
        if (!empty($data) && !array_key_exists('type', $data)) {
            $data['type'] = $this->api_type;
        }
        if (!empty($data) && !empty($this->hateoas) &&
            !array_key_exists('links', $data)) {
            $data['links'] = $this->hateoas;
        }
        $output = $this->filterOutput($request, $data, $code);
        return $output;

    }


    public function getDepartment($id)
    {
        global $Departments;

        $output = array();

        if (array_key_exists($id, $Departments)) {
            $output = array('Name' => $id);
            $output = array_merge($output, $Departments[$id]);
            return $output;
        } else {
            foreach ($Departments as $key => $dept) {
                if ($dept['id'] == $id) {
                    $output = array('Name' => $key);
                    $output = array_merge($output, $dept);
                    return $output;
                }
            }
        }
        return null;

    }


    /* END BaseController */
}
