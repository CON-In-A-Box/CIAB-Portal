<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

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
        $this->api_type = 'log';
        if (!\ciab\RBAC::havePermission("api.get.log")) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }

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
