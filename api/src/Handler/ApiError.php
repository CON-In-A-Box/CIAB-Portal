<?php declare(strict_types=1);
/*.
    require_module 'standard';
    require_module 'json';
.*/

namespace App\Handler;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class ApiError extends \Slim\Handlers\Error
{

    protected function errorResponse(Request $request, Response $response, string $message, $status, int $code):  Response
    {
        $result = [
        'type' => 'error',
        'code' => $code,
        'status' => $status,
        'message' => $message,
        ];
        return $response->withJson($result, $code);

    }


    public function __invoke(Request $request, Response $response, \Exception $exception)
    {

        if (is_a($exception, '\App\Error\NotFoundException')) {
            return $this->errorResponse($request, $response, $exception->getMessage(), 'Not Found', 404);
        } elseif (is_a($exception, '\App\Error\PermissionDeniedException')) {
            return $this->errorResponse($request, $response, $exception->getMessage(), 'Permission Denied', 403);
        } elseif (is_a($exception, '\App\Error\InvalidParameterException')) {
            return $this->errorResponse($request, $response, $exception->getMessage(), 'Invalid Parameter', 400);
        } elseif (is_a($exception, '\App\Error\ConflictException'))  {
            return $this->errorResponse($request, $response, $exception->getMessage(), 'Conflict', 409);
        } elseif (is_a($exception, '\App\Error\InternalServerErrorException')) {
            return $this->errorResponse($request, $response, $exception->getMessage(), 'Internal Server Error', 500);
        }

        $statusCode = 500;
        if (is_int($exception->getCode()) && $exception->getCode() !== 0 && $exception->getCode() < 599) {
            $statusCode = $exception->getCode();
        }
        $className = new \ReflectionClass(get_class($exception));
        $data = [
        'message' => $exception->getMessage(),
        'status' => $className->getShortName(),
        'code' => $statusCode,
        'type' => 'error',
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString(),
        ];
        $body = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        error_log($body);
        return $response->withStatus($statusCode)->withHeader('Content-type', 'application/problem+json')->write($body);

    }


    /* End ApiError Class */
}
