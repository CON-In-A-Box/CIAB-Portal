<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"administrative"},
 *      path="/admin/configuration/{field}",
 *      summary="Get a configuration setting for the site",
 *      @OA\Parameter(
 *          description="Configuration setting field",
 *          in="path",
 *          name="field",
 *          required=true,
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="OK",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/configuration"
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/configuration_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 *
 *  @OA\Get(
 *      tags={"administrative"},
 *      path="/admin/configuration",
 *      summary="Get all configuration settings for the site",
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
 *              ref="#/components/schemas/configuration_list"
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/configuration_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Controller\System;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;

class GetConfiguration extends BaseSystem
{


    use \App\Controller\TraitConfiguration;


    protected function buildExtendedConfQuery(&$select, $params)
    {
        $union = $select->union();
        $union->columns(
            'Field',
            "'Configuration' as TargetTable",
            'null as Type',
            'null as InitialValue',
            'null as Description',
            'Value'
        )->from(
            'Configuration'
        )->where(
            '`Field` NOT IN ',
            $union->subselect()->columns('Field')->from('ConfigurationField')->whereEquals(['TargetTable' => 'Configuration'])
        );

        if (array_key_exists('key', $params)) {
            $union->whereEquals(['Field' => $params['key']]);
        }

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.get.configuration'];
        $this->checkPermissions($permissions);

        $user = $request->getAttribute('oauth2-token')['user_id'];

        $result = $this->getConfiguration($params, 'Configuration');
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
        $result[0]['type'] = 'configuration_entry';
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $result[0]
        ];

    }


    /* end GetConfiguration */
}
