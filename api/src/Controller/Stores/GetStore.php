<?php declare(strict_types=1);

/**
 *  @OA\Get(
 *      tags={"stores"},
 *      path="/stores/{id}",
 *      summary="Gets a store",
 *      deprecated=true,
 *      @OA\Parameter(
 *          description="Id of the store.",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Store found",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/store"
 *          )
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/store_not_found"
 *      ),
 *      security={{"ciab_auth": {}}}
 *  )
 */
namespace App\Controller\Stores;

use Slim\Http\Request;
use Slim\Http\Response;

class GetStore extends BaseStore
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $store = $this->getStore($params, $request, $response, $error);

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $store
        ];

    }


  /* end GetStore */
}
