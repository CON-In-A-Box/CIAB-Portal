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
        $permissions = ['api.get.configuration'];
        $this->checkPermissions($permissions);

        $user = $request->getAttribute('oauth2-token')['user_id'];

        $sql = <<<SQL
            UNION
            SELECT
                `Field`,
                'Configuration' as TargetTable,
                null as Type,
                null as InitialValue,
                null as Description,
                Value
            FROM
                `Configuration`
            WHERE
                `Field` NOT IN (
                SELECT
                    `Field`
                FROM
                    `ConfigurationField`
                WHERE
                    TargetTable = 'Configuration'
            )
SQL;
        if (array_key_exists('key', $args)) {
            $sql .= " AND `Field` = '{$args['key']}'";
        }

        $result = $this->getConfiguration($args, 'Configuration', null, $sql);
        foreach ($result as $index => $entry) {
            if (array_key_exists($entry['field'], $_ENV)) {
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
