<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\System;

use Slim\Http\Request;
use Slim\Http\Response;

class GetConfiguration extends BaseSystem
{


    use \App\Controller\TraitConfiguration;


    public function buildResource(Request $request, Response $response, $args): array
    {
        if (!\ciab\RBAC::havePermission("api.get.configuration")) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }
        $user = $request->getAttribute('oauth2-token')['user_id'];

        $sql = <<<SQL
            UNION
            SELECT
                Field,
                'Configuration' as TargetTable,
                null as Type,
                null as InitialValue,
                null as Description,
                Value
            FROM
                `Configuration`
            WHERE
                Field NOT IN (
                SELECT
                    Field
                FROM
                    `ConfigurationField`
                WHERE
                    TargetTable = 'Configuration'
            )
SQL;

        $result = $this->getConfiguration($args, 'Configuration', null, $sql);
        foreach ($result as $index->$entry) {
            if (array_key_exists($entry['Field'], $_ENV)) {
                unset($result[$index]);
            }
        }

        if (count($result) > 1) {
            $output = [];
            $output['type'] = 'configuration_list';
            return [
            \App\Controller\BaseController::LIST_TYPE,
            $result,
            $output
            ];
        }
        $result[0]['type'] = 'configuration';
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $result[0]
        ];

    }


    /* end GetConfiguration */
}
