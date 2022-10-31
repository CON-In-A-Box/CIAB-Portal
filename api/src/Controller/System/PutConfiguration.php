<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Put(
 *      tags={"administrative"},
 *      path="/admin/configuration",
 *      summary="Updates a site configuration setting",
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="Field",
 *                      type="string",
 *                      nullable=false
 *                  ),
 *                  @OA\Property(
 *                      property="Value",
 *                      type="string",
 *                      nullable=false
 *                  ),
 *              )
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
 **/

namespace App\Controller\System;

use Slim\Http\Request;
use Slim\Http\Response;

use App\Controller\InvalidParameterException;

class PutConfiguration extends BaseSystem
{

    use \App\Controller\TraitConfiguration;


    public function buildResource(Request $request, Response $response, $args): array
    {
        $permissions = ['api.put.configuration'];
        $this->checkPermissions($permissions);
        $body = $request->getParsedBody();
        if (empty($body)) {
            throw new InvalidParameterException('No update parameter present');
        }
        $this->putConfiguration($request, $response, $args, 'Configuration', $body);

        $target = new GetConfiguration($this->container);
        $args['key'] = $body['Field'];
        $data = $target->buildResource($request, $response, $args)[1];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data)
        ];

    }


    /* end PutConfiguration */
}
