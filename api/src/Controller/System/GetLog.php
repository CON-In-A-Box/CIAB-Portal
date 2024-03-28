<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 * @OA\Schema(
 *      schema="log_entry",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"log_entry"}
 *      ),
 *      @OA\Property(
 *          property="id",
 *          description="Entry ID",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="account",
 *          description="Member account generating the log",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="function",
 *          description="Function generating the log",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="query",
 *          description="The query string being logged",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="date",
 *          description="When the log entry was generated",
 *          type="string",
 *          format="date-time",
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="log",
 *      allOf = {
 *          @OA\Schema(ref="#/components/schemas/resource_list")
 *      },
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"log"}
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="List of entries",
 *          @OA\Items(
 *              ref="#/components/schemas/log_entry"
 *          ),
 *      )
 *  )
 *
 *  @OA\Get(
 *      tags={"administrative"},
 *      path="/admin/log",
 *      summary="Read the system log",
 *      @OA\Parameter(
 *          ref="#/components/parameters/max_results",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/page_token",
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="OK",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/log"
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          description="Log entries not found on the system.",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/error"
 *          )
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/


namespace App\Controller\System;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;

class GetLog extends BaseSystem
{

    protected static $columnsToAttributes = [
    '"log_entry"' => 'type',
    'LogEntryID' => 'id',
    'AccountID' => 'account',
    'Function' => 'function',
    'Query' => 'query',
    'Date' => 'date'
    ];


    private function filterLog($query)
    {
        $pattern = ["/(Authentication = ')(.*)(')/i", "/(OneTime = ')(.*)(')/i"];
        $replace = '\1&lt;REDACTED&gt;\3';
        return(preg_replace($pattern, $replace, $query));

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.get.log'];
        $this->checkPermissions($permissions);

        $this->api_type = 'log';
        $select = Select::new($this->container->db);
        $data = $select->columns(...GetLog::selectMapping())
            ->from($select->subselect()
                ->columns('*')
                ->from('ActivityLog')
                ->as('sub')
                ->getStatement())
            ->orderBy('LogEntryID DESC')
            ->fetchAll();

        foreach ($data as $idx => $line) {
            $data[$idx]['query'] = $this->filterLog($line['query']);
            $data[$idx]['query'] = trim(preg_replace('/\s+/', ' ', $data[$idx]['query']));
            $datetime = \DateTime::createFromFormat('Y-m-d H:i:s', $data[$idx]['date']);
            $data[$idx]['date'] = $datetime->format(\DateTime::RFC3339);
        }

        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        array('type' => 'log')];

    }


    /* end GetLog */
}
