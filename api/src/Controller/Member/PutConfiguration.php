<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Put(
 *      tags={"members"},
 *      path="/member/{id}/configuration",
 *      summary="Updates a member configuration setting",
 *      @OA\Parameter(
 *          description="The id or login of the member",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(
 *              oneOf = {
 *                  @OA\Schema(
 *                      description="Member login",
 *                      type="string"
 *                  ),
 *                  @OA\Schema(
 *                      description="Member id",
 *                      type="integer"
 *                  )
 *              }
 *          )
 *      ),
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

namespace App\Controller\Member;

use Slim\Http\Request;
use Slim\Http\Response;

use App\Error\PermissionDeniedException;

class PutConfiguration extends BaseMember
{

    use \App\Controller\TraitConfiguration;


    public function buildResource(Request $request, Response $response, $params): array
    {
        $user = $request->getAttribute('oauth2-token')['user_id'];
        if (array_key_exists('id', $params)) {
            $accountID = $this->getMember($request, $params['id'])[0]['id'];
        } else {
            $accountID = $user;
        }

        if ($accountID != $user &&
            !$this->container->RBAC->havePermission("api.put.member")) {
            throw new PermissionDeniedException();
        }

        $body = $request->getParsedBody();
        $body['AccountID'] = $accountID;
        $this->putConfiguration($request, $response, $params, 'AccountConfiguration', $body);

        $target = new GetConfiguration($this->container);
        $params['key'] = $body['Field'];
        $data = $target->buildResource($request, $response, $params)[1];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data)
        ];

    }


    /* end PutConfiguration */
}
