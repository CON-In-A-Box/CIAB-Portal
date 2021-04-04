<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Schema(
 *      schema="error",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"error"}
 *      ),
 *      @OA\Property(
 *          property="code",
 *          type="integer",
 *          description="announcement ID"
 *      ),
 *      @OA\Property(
 *          property="status",
 *          type="string",
 *          description="Error Status"
 *      ),
 *      @OA\Property(
 *          property="message",
 *          type="string",
 *          description="Error Message"
 *      )
 *  )
 *
 *  @OA\Parameter(
 *      parameter="maxResults",
 *      description="Maximum members of the list per page or 'all' (default 100).",
 *      in="query",
 *      name="maxResults",
 *      required=false,
 *      style="form",
 *      @OA\Schema(
 *          oneOf={
 *              @OA\Schema(
 *                  type="integer"
 *              ),
 *              @OA\Schema(
 *                  type="string",
 *                  enum={"all"}
 *              ),
 *          }
 *      )
 *  )
 *
 *  @OA\Parameter(
 *      parameter="pageToken",
 *      description="Starting page of results.",
 *      in="query",
 *      name="pageToken",
 *      required=false,
 *      style="form",
 *      @OA\Schema(
 *          type="integer",
 *      )
 *  )
 *
 *  @OA\Parameter(
 *      parameter="short_response",
 *      description="Do not include sub-resource structures, only Ids.",
 *      in="query",
 *      name="short_response",
 *      required=false,
 *      style="form",
 *      @OA\Schema(
 *          type="integer",
 *          enum={0, 1}
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="resource_list",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="List of resources",
 *          @OA\Items(
 *              @OA\Schema(type="object")
 *          )
 *      ),
 *      @OA\Property(
 *          property="nextPageToken",
 *          description="If present, the `pageToken` for the next page of results",
 *          type="integer",
 *      )
 *  )
 */

namespace App\Controller;

use ArrayObject;
use Exception;

use Slim\Container;
use Slim\Http\Response;
use Slim\Http\Request;
use Slim\Http\Environment;
use Atlas\Query\Select;

require_once __DIR__.'/../../../backends/RBAC.inc';

class NotFoundException extends Exception
{

}


class PermissionDeniedException extends Exception
{

}


class InvalidParameterException extends Exception
{

}


class ConflictException extends Exception
{

}

class IncludeResource
{

    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $parameter;


    public function __construct(string $class, string $parameter, string $field)
    {
        $this->class = $class;
        $this->field = $field;
        $this->parameter = $parameter;

    }


    public function process(Request $request, Response $response, Container $container, array $params, array &$data, array $history): void
    {
        if (in_array($this->field, array_keys($data), true) &&
            $data[$this->field] !== null) {
            if (!array_key_exists($this->class, $history)) {
                $history[$this->class] = [];
            }
            if (in_array($data[$this->field], $history[$this->class])) {
                return;
            }
            $newparams = $params;
            $newparams[$this->parameter] = $data[$this->field];
            $target = new $this->class($container);
            try {
                $newdata = $target->buildResource($request, $response, $newparams)[1];
            } catch (Exception $e) {
                return;
            }
            $history[$this->class][] = $data[$this->field];
            $target->processIncludes($request, $response, $params, $newdata, $history);
            $data[$this->field] = $target->arrayResponse($request, $response, $newdata);
        }

    }


    /* End IncludeResource */
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
    protected $chain;

    /**
     * @var array[]
    */
    protected $includes;

    /**
     * @var array[]
    */
    protected static $columnsToAttributes = null;


    protected function __construct(string $api_type, Container $container)
    {
        $this->api_type = $api_type;
        $this->container = $container;
        $this->chain = [];
        $this->includes = [];

        if (array_key_exists('Neon', $GLOBALS)) {
            \loadDefinedFields();
        }

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
        } catch (InvalidParameterException $e) {
            $result = [
            BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, $e->getMessage(), 'Invalid Parameter', 400)
            ];
        } catch (ConflictException $e) {
            $result = [
            BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, $e->getMessage(), 'Conflict', 409)
            ];
        }

        if ($result === null || $result[0] === null) {
            return null;
        }
        $type = $result[0];
        $data = $result[1];
        if ($type == BaseController::LIST_TYPE) {
            $output = $result[2];
            if (count($result) == 4) {
                $code = $result[3];
            } else {
                $code = 200;
            }

            return $this->handleListType($request, $response, $output, $data, $args, $code);
        }
        if (count($result) == 3) {
            $code = $result[2];
        } else {
            $code = 200;
        }
        if ($type == BaseController::RESULT_TYPE) {
            if (is_a($data, 'Slim\Http\Response')) {
                return $data;
            } else {
                return $this->jsonResponse($request, $response, $data, $code);
            }
        } else {
            return $this->handleResourceType($request, $response, $data, $args, $code);
        }

    }


    public function handleListType(Request $request, Response $response, array $output, array $data, array $params, int $code = 200)
    {
        $short = $request->getQueryParam('short_response', false);
        if (!boolval($short)) {
            for ($i = 0; $i < count($data); $i++) {
                $this->processIncludes($request, $response, $params, $data[$i]);
            }
        }
        return $this->listResponse($request, $response, $output, $data, $code);

    }


    public function handleResourceType(Request $request, Response $response, $data, array $params, $code = 200)
    {
        $short = $request->getQueryParam('short_response', false);
        if (!boolval($short)) {
            $this->processIncludes($request, $response, $params, $data);
        }
        return $this->jsonResponse($request, $response, $data, $code);

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


    protected static function filterBodyParams(array $permitted_keys, array $body): array
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
            $data = $child->handle($request, $response, $data, $code, $this->container);
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


    public function arrayResponse(Request $request, Response $response, $data, $code = 200): Array
    {
        foreach ($this->chain as $child) {
            $data = $child->handle($request, $response, $data, $code, $this->container);
        }
        if (!empty($data) && !array_key_exists('type', $data)) {
            $data['type'] = $this->api_type;
        }
        $output = $this->filterOutput($request, $data, $code);
        return $output;

    }


    public function getDepartment($id)
    {
        $select = Select::new($this->container->db);
        $select->columns('*');
        $select->columns($select->subselect()->columns('COUNT(DepartmentID)')->from('`Departments` d2')->whereEquals(['d2.ParentDepartmentID' => 'd1.DepartmentID'])->andWhere("NOT NAME = 'Historical Placeholder'")->as('childCount')->getStatement());
        $select->columns($select->subselect()->columns('GROUP_CONCAT(Email)')->from('EMails')->whereEquals(['DepartmentID' => 'd1.DepartmentID'])->as('email')->getStatement());
        $select->from('Departments d1');
        if ($id !== null) {
            $select->where("(DepartmentID = '$id' OR Name = '$id')");
        }
        $placeholders = $select->subselect()->columns('DepartmentID')->from('Departments')->whereEquals(['Name' => 'Historical Placeholder']);
        $select->where('DepartmentID NOT IN ', $placeholders);
        $select->where('ParentDepartmentID NOT IN ', $placeholders);
        $data = $select->fetchAll();

        $final = [];
        if (empty($data)) {
            throw new NotFoundException("Department '$id' Not Found");
        }
        foreach ($data as $entry) {
            $output = [];
            $output['id'] = $entry['DepartmentID'];
            if ($entry['DepartmentID'] != $entry['ParentDepartmentID']) {
                $output['parent'] = $entry['ParentDepartmentID'];
            } else {
                $output['parent'] = null;
            }
            unset($entry['DepartmentID']);
            unset($entry['ParentDepartmentID']);
            foreach ($entry as $key => $value) {
                $key = lcfirst($key);
                $key = str_replace('ID', '', $key);
                $output[$key] = $value;
            }
            if ($output['email']) {
                $v = explode(',', $output['email']);
            } else {
                $v = [];
            }
            $output['email'] = $v;
            if ($id != null) {
                return $output;
            }
            $final[] = $output;
        }
        return $final;

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


    public function processIncludes(Request $request, Response $response, $params, &$data, array $history = [])
    {
        foreach ($this->includes as $target) {
            $target->process($request, $response, $this->container, $params, $data, $history);
        }

    }


    protected function getEvent(string $id)
    {
        $event = new \App\Controller\Event\GetEvent($this->container);
        $env = Environment::mock([]);
        $request = Request::createFromEnvironment($env);
        $response = new Response();
        return $event->buildResource($request, $response, ['id' => $id])[1];

    }


    protected function checkRequiredBody(Request $request, array $required_params)
    {
        $body = $request->getParsedBody();
        if (empty($body)) {
            throw new InvalidParameterException('Required body not present');
        }
        foreach ($required_params as $required) {
            if (!array_key_exists($required, $body) || $body[$required] === null) {
                throw new InvalidParameterException("Required '$required' parameter not present");
            }
        }

        return $body;

    }


    protected static function attributesToColumns(): array
    {
        if (static::$columnsToAttributes !== null) {
            return array_flip(static::$columnsToAttributes);
        }
        return null;

    }


    protected static function selectMapping(): array
    {
        if (static::$columnsToAttributes !== null) {
            $ret = array();
            foreach (static::$columnsToAttributes as $key => $value) {
                $ret[] = "$key AS $value";
            }
            return $ret;
        }
        return ['*'];

    }


    public static function insertPayloadFromParams(array $params, $includeId = true): array
    {
        $paramsToColumns = static::attributesToColumns();
        $params = static::filterBodyParams(array_keys($paramsToColumns), $params);

        $ret = array();

        if ($includeId && !array_key_exists('id', $params)) {
            $ret[$paramsToColumns['id']] = null;
        };

        foreach ($params as $key => $val) {
            $ret[$paramsToColumns[$key]] = $val;
        }

        if (empty($ret)) {
            throw new InvalidParameterException("No parameters not present");
        }

        return $ret;

    }


    /* END BaseController */
}
