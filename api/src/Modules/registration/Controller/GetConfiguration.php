<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"registration"},
 *      path="/registration/admin/configuration/{field}",
 *      summary="Get a configuration setting for registration",
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
 *      tags={"registration"},
 *      path="/registration/admin/configuration",
 *      summary="Get all configuration settings for registration",
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

namespace App\Modules\registration\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

class GetConfiguration extends BaseRegistration
{


    use \App\Controller\TraitConfiguration;


    public function buildResource(Request $request, Response $response, $args): array
    {
        $result = $this->getConfiguration($args, 'Registration_Configuration');

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
