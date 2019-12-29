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


    protected function __construct(string $api_type, Container $container)
    {
        $this->api_type = $api_type;
        $this->container = $container;
        $hateoas = [];

    }


    protected function addHateoasLink(string $method, string $href, string $request)
    {
        $this->hateoas[] = [
        'method' => $method,
        'href' => $href,
        'request' => $request
        ];

    }


    protected function jsonResponse(Request $request, Response $response, $data, $code = 200): Response
    {
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
        return $response->withJson($data, $code, $parameters);

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
