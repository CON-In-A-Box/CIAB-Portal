<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller;

use Slim\Container;
use Slim\Http\Response;
use Slim\Http\Request;

abstract class BaseVendorController extends BaseController
{

    protected static $instance;


    protected function __construct(string $api_type, Container $container)
    {
        $basename = explode('\\', get_class($this));
        if (strcasecmp($basename[0], 'App') == 0) {
            array_shift($basename);
            array_shift($basename);
            array_unshift($basename, 'Vendor');
            $vendor = implode('\\', $basename);
            if (class_exists($vendor)) {
                self::$instance = new $vendor($container);
            }
        }
        parent::__construct($api_type, $container);

    }


    abstract public function baseBuildResource(Request $request, Response $response, $args): array;


    public function buildResource(Request $request, Response $response, $args): array
    {
        if (self::$instance !== null &&
            method_exists(self::$instance, __FUNCTION__)) {
            return self::$instance->{__FUNCTION__}(...func_get_args());
        } else {
            return $this->baseBuildResource(...func_get_args());
        }

    }


    abstract public static function baseInstall($container): void;


    abstract public static function basePermissions($database): ?array;


    public static function install($container): void
    {
        if (self::$instance !== null &&
            method_exists(self::$instance, __FUNCTION__)) {
            self::$instance->{__FUNCTION__}($container);
        } else {
            self::baseInstall($container);
        }

    }


    public static function permissions($database): ?array
    {
        if (self::$instance !== null &&
            method_exists(self::$instance, __FUNCTION__)) {
            return self::$instance->{__FUNCTION__}($database);
        } else {
            return self::basePermissions($database);
        }

    }


    public function __invoke(Request $request, Response $response, $args)
    {
        if (self::$instance !== null &&
            method_exists(self::$instance, __FUNCTION__)) {
            return self::$instance->{__FUNCTION__}(...func_get_args());
        } else {
            return parent::{__FUNCTION__}(...func_get_args());
        }

    }


    public function handleListType(Request $request, Response $response, array $output, array $data, array $params, int $code = 200)
    {
        if (self::$instance !== null &&
            method_exists(self::$instance, __FUNCTION__)) {
            return self::$instance->{__FUNCTION__}(...func_get_args());
        } else {
            return parent::{__FUNCTION__}(...func_get_args());
        }

    }


    public function handleResourceType(Request $request, Response $response, $data, array $params, $code = 200)
    {
        if (self::$instance !== null &&
            method_exists(self::$instance, __FUNCTION__)) {
            return self::$instance->{__FUNCTION__}(...func_get_args());
        } else {
            return parent::{__FUNCTION__}(...func_get_args());
        }

    }


    protected function filterOutput(Request $request, $data, $code): array
    {
        if (self::$instance !== null &&
            method_exists(self::$instance, __FUNCTION__)) {
            return self::$instance->{__FUNCTION__}(...func_get_args());
        } else {
            return parent::{__FUNCTION__}(...func_get_args());
        }

    }


    protected function jsonResponse(Request $request, Response $response, $data, $code = 200): Response
    {
        if (self::$instance !== null &&
            method_exists(self::$instance, __FUNCTION__)) {
            return self::$instance->{__FUNCTION__}(...func_get_args());
        } else {
            return parent::{__FUNCTION__}(...func_get_args());
        }

    }


    protected function errorResponse(Request $request, Response $response, string $status, $message, int $code):  Response
    {
        if (self::$instance !== null &&
            method_exists(self::$instance, __FUNCTION__)) {
            return self::$instance->{__FUNCTION__}(...func_get_args());
        } else {
            return parent::{__FUNCTION__}(...func_get_args());
        }

    }


    protected function listResponse(Request $request, Response $response, $output, $data, $code = 200): Response
    {
        if (self::$instance !== null &&
            method_exists(self::$instance, __FUNCTION__)) {
            return self::$instance->{__FUNCTION__}(...func_get_args());
        } else {
            return parent::{__FUNCTION__}(...func_get_args());
        }

    }


    public function arrayResponse(Request $request, Response $response, $data, $code = 200): Array
    {
        if (self::$instance !== null &&
            method_exists(self::$instance, __FUNCTION__)) {
            return self::$instance->{__FUNCTION__}(...func_get_args());
        } else {
            return parent::{__FUNCTION__}(...func_get_args());
        }

    }


    public function getDepartment($id)
    {
        if (self::$instance !== null &&
            method_exists(self::$instance, __FUNCTION__)) {
            return self::$instance->{__FUNCTION__}(...func_get_args());
        } else {
            return parent::{__FUNCTION__}(...func_get_args());
        }

    }


    public function notFoundResponse(
        Request $request,
        Response $response,
        String $type,
        string $key
    ): Response {
        if (self::$instance !== null &&
            method_exists(self::$instance, __FUNCTION__)) {
            return self::$instance->{__FUNCTION__}(...func_get_args());
        } else {
            return parent::{__FUNCTION__}(...func_get_args());
        }

    }


    public function checkPermissions(
        array $permissions,
        string $message = 'Permission Denied'
    ) {
        if (self::$instance !== null &&
            method_exists(self::$instance, __FUNCTION__)) {
            return self::$instance->{__FUNCTION__}(...func_get_args());
        } else {
            return parent::{__FUNCTION__}(...func_get_args());
        }

    }


    protected function getEvent(string $id)
    {
        if (self::$instance !== null &&
            method_exists(self::$instance, __FUNCTION__)) {
            return self::$instance->{__FUNCTION__}(...func_get_args());
        } else {
            return parent::{__FUNCTION__}(...func_get_args());
        }

    }


    protected function getMember(Request $request, string $id, string $from = null, bool $internal = true)
    {
        if (self::$instance !== null &&
            method_exists(self::$instance, __FUNCTION__)) {
            return self::$instance->{__FUNCTION__}(...func_get_args());
        } else {
            return parent::{__FUNCTION__}(...func_get_args());
        }

    }


    protected function checkRequiredBody(Request $request, array $required_params)
    {
        if (self::$instance !== null &&
            method_exists(self::$instance, __FUNCTION__)) {
            return self::$instance->{__FUNCTION__}(...func_get_args());
        } else {
            return parent::{__FUNCTION__}(...func_get_args());
        }

    }


    /* END BaseVendorController */
}
