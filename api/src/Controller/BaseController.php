<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Parameter(
 *      parameter="max_results",
 *      description="Maximum members of the list per page or 'all' (default 100).",
 *      in="query",
 *      name="max_results",
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
 *      parameter="page_token",
 *      description="Starting page of results.",
 *      in="query",
 *      name="page_token",
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
 *  @OA\Parameter(
 *      parameter="event",
 *      description="Target event ID for query. If not specified defaults to the current event",
 *      in="query",
 *      name="short_response",
 *      required=false,
 *      style="form",
 *      @OA\Schema(
 *          type="integer",
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
 *          property="next_page_token",
 *          description="If present, the `page_token` for the next page of results",
 *          type="integer",
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="BaseController"
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

use App\Error\NotFoundException;
use App\Error\PermissionDeniedException;
use App\Error\InvalidParameterException;

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
    public $includes;

    /**
     * @var array[]
    */
    protected static $columnsToAttributes = null;

    /**
     * @var int
    */
    protected $currentEvent = -1;


    protected function __construct(string $api_type, Container $container)
    {
        $this->api_type = $api_type;
        $this->container = $container;
        $this->chain = [];
        $this->includes = null;

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
        $result = $this->buildResource($request, $response, $args);

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
            if (is_a($data, 'Slim\Http\Response') || !is_array($data)) {
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
        if ($this->includes !== null) {
            $cleandata = array_values($data);
            foreach ($cleandata as $index => $entry) {
                IncludeResource::processIncludes($this->includes, $request, $response, $this->container, $params, $cleandata[$index]);
            }
        } else {
            $cleandata = $data;
        }
        return $this->listResponse($request, $response, $output, $cleandata, $code);

    }


    public function handleResourceType(Request $request, Response $response, $data, array $params, $code = 200)
    {
        IncludeResource::processIncludes($this->includes, $request, $response, $this->container, $params, $data);
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

        if (!empty($data) && !array_key_exists('type', $data)) {
            $data['type'] = $this->api_type;
        }
        $output = $this->filterOutput($request, $data, $code);
        return $response->withJson($output, $code);

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
        $index = intval($request->getQueryParam('page_token', 0));
        $maxPage = $request->getQueryParam('max_results', 100);
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
            $output['next_page_token'] = $index + $count;
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
        return BaseController::staticGetDepartment($this->container, $id);

    }


    public static function staticGetDepartment($container, $id)
    {
        $select = Select::new($container->db);
        $select->columns('*')
            ->columns(
                $select->subselect()
                    ->columns('COUNT(DepartmentID)')
                    ->from('`Departments` d2')
                    ->where('d2.ParentDepartmentID = d1.DepartmentID')
                    ->andWhere("NOT NAME = 'Historical Placeholder'")
                    ->as('child_count')
                    ->getStatement()
            )
            ->columns(
                $select->subselect()
                    ->columns('GROUP_CONCAT(Email)')
                    ->from('EMails')
                    ->where('DepartmentID = d1.DepartmentID')
                    ->as('email')
                    ->getStatement()
            )
            ->from('Departments d1');
        if ($id !== null) {
            $select->orWhere('(')
                ->catWhere('DepartmentID = ', $id)
                ->catWhere(' OR ')
                ->catWhere('Name = ', $id)
                ->catWhere(')');
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
            if (!$valid && $this->container->RBAC->havePermission($perm)) {
                $valid = true;
            }
        }
        if (!$valid) {
            throw new PermissionDeniedException($message);
        }

    }


    public static function staticGetEvent($container, string $id)
    {
        $data = $container->EventService->getById($id);
        if (empty($data)) {
            throw new NotFoundException("Event '{$id}' Not Found");
        }
        return $data[0];

    }


    protected function getEvent(string $id)
    {
        return $this->staticGetEvent($this->container, $id);

    }


    protected function getEventId(Request $request)
    {
        $event = $request->getQueryParam('event', 'current');
        return $this->getEvent($event)['id'];

    }


    protected function getMember(Request $request, string $id, string $from = null, bool $internal = true)
    {
        if ($from === null) {
            $from = 'id,email';
        }
        if ($id === 'current') {
            $id = $request->getAttribute('oauth2-token')['user_id'];
        }
        $query = "q=".urlencode($id)."&from=$from";
        $lookup = new \App\Controller\Member\FindMembers($this->container);
        $lookup->internal = $internal;
        $env = Environment::mock(['QUERY_STRING' => $query]);
        $new_request = Request::createFromEnvironment($env);
        $new_request = $new_request->withAttribute(
            'oauth2-token',
            $request->getAttribute('oauth2-token')
        );
        $response = new Response();
        return $lookup->buildResource($new_request, $response, [])[1];

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
            if (is_string($val)) {
                $ret[$paramsToColumns[$key]] = htmlspecialchars_decode($val, ENT_QUOTES);
            } else {
                $ret[$paramsToColumns[$key]] = $val;
            }
        }

        if (empty($ret)) {
            throw new InvalidParameterException("No parameters not present");
        }

        return $ret;

    }


    public function currentEvent()
    {
        /* Place 1 - Look for it in the session */

        if ($this->currentEvent != -1) {
            return $this->currentEvent;
        }

        if (isset($_SESSION['CurrentEvent'])) {
            return $_SESSION['CurrentEvent'];
        }

        /* Place 2 - try to get it from the database */

        $value = Select::new($this->container->db)
            ->columns('Value')
            ->from('Configuration')
            ->whereEquals(['Field' => 'CurrentEventID'])
            ->fetchOne();
        if ($value) {
            $this->currentEvent = $value['CurrentEventID'];
            return $this->currentEvent;
        }

        /* Place 3 - current calander year */

        $value = Select::new($this->container->db)
            ->columns('EventID')
            ->from('Events')
            ->where('`DateTo` >= NOW()')
            ->orderBy('DateFrom ASC LIMIT 1')
            ->fetchOne();
        if ($value) {
            $this->currentEvent = $value['EventID'];
            return $this->currentEvent;
        }

        /* Fallback - Last year in database*/

        $value = Select::new($this->container->db)
            ->columns('EventID')
            ->from('Events')
            ->orderBy('EventID DESC LIMIT 1')
            ->fetchOne();
        if ($value) {
            $this->currentEvent = $value['EventID'];
            return $this->currentEvent;
        }

        return $this->currentEvent;

    }


    public function getContainer(): Container
    {
        return $this->container;

    }


    abstract public static function install($container): void;


    abstract public static function permissions($database): ?array;


    /* END BaseController */
}
