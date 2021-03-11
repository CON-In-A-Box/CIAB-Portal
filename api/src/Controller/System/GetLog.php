<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *
 * @OA\Schema(
 *      schema="log_entry",
 *      @OA\Property(
 *          property="LogEntryID",
 *          description="Entry ID",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="AccountID",
 *          description="Member account generating the log",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="Function",
 *          description="Function generating the log",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="Query",
 *          description="The query string being logged",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="Date",
 *          description="When the log entry was generated",
 *          type="string",
 *          format="date-time",
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="log",
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
 *      @OA\Response(
 *          response=200,
 *          description="Log entries found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/log"
 *          ),
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

class GetLog extends BaseSystem
{


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
        $sql = "SELECT MAX(LogEntryID) as mid FROM ActivityLog;";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $sth->fetch();
        $max = intval($data['mid']);
        $limit = 1000;
        if (array_key_exists('lines', $params)) {
            $limit = intval($params['lines']);
        }
        $max = $max - $limit;

        $sql = <<<SQL
            SELECT * FROM ( SELECT * FROM ActivityLog WHERE LogEntryID > $max)   sub ORDER BY LogEntryID DESC;
SQL;
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll();

        foreach ($data as $idx => $line) {
            $data[$idx]['Query'] = $this->filterLog($line['Query']);
        }

        $result = [];
        $result['data'] = $data;
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $result
        ];

    }


    /* end GetLog */
}
