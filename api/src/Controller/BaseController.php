<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller;

use Slim\Container;
use Slim\Http\Response;
use Slim\Http\Request;

require_once __DIR__.'/../../../backends/RBAC.inc';

abstract class BaseController
{

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string
     */
    protected static $api_type;


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


    /* END BaseController */
}
