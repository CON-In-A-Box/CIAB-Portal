<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller;

use ArrayObject;
use Exception;

use Slim\Container;
use Slim\Http\Response;
use Slim\Http\Request;

require_once __DIR__.'/../../../backends/RBAC.inc';
require_once __DIR__.'/../../../functions/divisional.inc';

class NotFoundException extends Exception
{

}


class PermissionDeniedException extends Exception
{

}

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

        \loadDefinedFields();

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
        try {
            $result = $this->buildResource($request, $response, $args);
        } catch (NotFoundException $e) {
            $result = [
            BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, $e->getMessage(), 'Not Found', 404)
            ];
        } catch (PermissionDeniedException $e) {
            $result = [
            BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, $e->getMessage(), 'Permission Denied', 403)
            ];
        }

        if ($result === null || $result[0] === null) {
            return null;
        }
        $type = $result[0];
        $data = $result[1];
        if ($type == BaseController::LIST_TYPE) {
            $output = $result[2];

            return $this->handleListType($request, $response, $output, $data);
        } elseif ($type == BaseController::RESULT_TYPE) {
            return $data;
        } else {
            if (count($result) == 3) {
                $code = $result[2];
            } else {
                $code = 200;
            }

            return $this->handleResourceType($request, $response, $data, $code);
        }

    }


    public function handleListType(Request $request, Response $response, array $output, array $data)
    {
        $includes = $request->getQueryParam('include', null);
        if ($includes) {
            $values = array_map('trim', explode(',', $includes));
            for ($i = 0; $i < count($data); $i++) {
                $this->processIncludes($request, $response, $args, $values, $data[$i]);
            }
        }
        return $this->listResponse($request, $response, $output, $data);

    }


    public function handleResourceType(Request $request, Response $response, $data, $code = 200)
    {
        $includes = $request->getQueryParam('include', null);
        if ($includes) {
            $values = array_map('trim', explode(',', $includes));
            $this->processIncludes($request, $response, $args, $values, $data);
        }
        return $this->jsonResponse($request, $response, $data, $code);

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


    protected function filterBodyParams(array $permitted_keys, array $body): array
    {
        $ret = (new ArrayObject($body))->getArrayCopy();
        $diff = array_diff(array_keys($body), $permitted_keys);
        foreach ($diff as $key) {
            unset($ret[$key]);
        }

        return $ret;

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


    private static function mapMemberData($input)
    {
        $map = ['Id' => 'id', 'First Name' => 'firstName',
        'Last Name' => 'lastName', 'Email' => 'email',
        'FirstName' => 'legalFirstName', 'LastName' => 'legalLastName',
        'MiddleName' => 'middleName', 'Suffix' => 'suffix',
        'Email2' => 'email2', 'Email3' => 'email3', 'Phone' => 'phone1',
        'Phone2' => 'phone2', 'AddressLine1' => 'addressLine1',
        'AddressLine2' => 'addressLine2', 'AddressCity' => 'city',
        'AddressState' => 'state', 'AddressZipCode' => 'zipCode',
        'AddressZipCodeSuffix' => 'zipPlus4', 'AddressCountry' => 'countryName',
        'AddressProvince' => 'province',
        'PreferredFirstName' => 'preferredFirstName',
        'PreferredLastName' => 'preferredLastName',
        'Deceased' => 'Deceased', 'DoNotContact' => 'DoNotContact',
        'EmailOptOut' => 'EmailOptOut', 'Birthdate' => 'Birthdate',
        'Gender' => 'Gender', 'DisplayPhone' => 'conComDisplayPhone'];
        $output = [];
        foreach ($map as $inField => $outField) {
            if (array_key_exists($inField, $input) && $input[$inField]) {
                $output[$outField] = $input[$inField];
            }
        }
        return $output;

    }


    public function findMember(
        Request $request,
        Response $response,
        $args,
        $key,
        $fields = null
    ) {
        if ($fields === null) {
            $fields = ['FirstName', 'MiddleName', 'LastName',
            'Suffix', 'Email2', 'Email3', 'Phone', 'Phone2',
            'AddressLine1', 'AddressLine2', 'AddressCity', 'AddressState',
            'AddressZipCode', 'AddressZipCodeSuffix', 'AddressCountry',
            'AddressProvince', 'PreferredFirstName', 'PreferredLastName',
            'Deceased', 'DoNotContact', 'EmailOptOut', 'Birthdate',
            'Gender', 'DisplayPhone'];
        }
        if ($args !== null && array_key_exists($key, $args)) {
            $data = \lookup_users_by_key($args[$key], true, true, false, $fields);
            if (empty($data['users'])) {
                throw new NotFoundException('Member Not Found');
            }
            $data = BaseController::mapMemberData($data['users'][0]);
        } else {
            $user = $request->getAttribute('oauth2-token')['user_id'];
            $data = \lookup_user_by_id($user, $fields);
            $data = BaseController::mapMemberData($data['users'][0]);
        }
        return $data;

    }


    public function findMemberId(
        Request $request,
        Response $response,
        $args,
        $key,
        $fields = null
    ) {
        if ($fields === null) {
            $fields = ['FirstName', 'MiddleName', 'LastName',
            'Suffix', 'Email2', 'Email3', 'Phone', 'Phone2',
            'AddressLine1', 'AddressLine2', 'AddressCity', 'AddressState',
            'AddressZipCode', 'AddressZipCodeSuffix', 'AddressCountry',
            'AddressProvince', 'PreferredFirstName', 'PreferredLastName',
            'Deceased', 'DoNotContact', 'EmailOptOut', 'Birthdate',
            'Gender', 'DisplayPhone'];
        }
        if ($args !== null && array_key_exists($key, $args) && $args[$key] !== 'current') {
            $user = $args[$key];
        } else {
            $user = $request->getAttribute('oauth2-token')['user_id'];
        }
        $data = \lookup_user_by_id($user, $fields);
        if (empty($data['users'])) {
            throw new NotFoundException('Member Not Found');
        }
        $data = BaseController::mapMemberData($data['users'][0]);
        return $data;

    }


    public function notFoundResponse(
        Request $request,
        Response $response,
        String $type,
        string $key
    ): Response {
        return $this->errorResponse(
            $request,
            $response,
            "Could not find $type ID $key",
            'Not Found',
            404
        );

    }


    public function checkPermissions(
        array $permissions,
        string $message = 'Permission Denied'
    ) {
        $valid = false;
        foreach ($permissions as $perm) {
            if (!$valid && \ciab\RBAC::havePermission($perm)) {
                $valid = true;
            }
        }
        if (!$valid) {
            throw new PermissionDeniedException($message);
        }

    }


    /* END BaseController */
}
