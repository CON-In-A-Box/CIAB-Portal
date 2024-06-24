<?php declare(strict_types=1);

/**
 *  @OA\Put(
 *      tags={"stores"},
 *      path="/stores/{id}",
 *      summary="Updates a store",
 *      deprecated=true,
 *      @OA\Parameter(
 *          description="Id of the store",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="store_slug",
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="name",
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="description",
 *                      type="string"
 *                  )
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="OK",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/store"
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/store_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 */
namespace App\Controller\Stores;

use Atlas\Query\Select;
use Atlas\Query\Update;
use Slim\Http\Request;
use Slim\Http\Response;

use App\Controller\BaseController;
use App\Error\NotFoundException;
use App\Error\PermissionDeniedException;

class PutStore extends BaseStore
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        // TODO: RBAC more than just admin maybe
        if (!$_SESSION['IS_ADMIN']) {
            throw new PermissionDeniedException();
        }

        $body = $request->getParsedBody();

        $update = Update::new($this->container->db);
        $update->table('Stores')->columns(BaseStore::insertPayloadFromParams($body, false));
        $update->whereEquals(['StoreID' => $params['id']]);
        $result = $update->perform();

        if ($result->rowCount() == 0) {
            throw new NotFoundException("Store ID ${params['id']} does not exist");
        }

        $store = $this->getStore($params, $request, $response, $error);

        return [
        BaseController::RESOURCE_TYPE,
        $store
        ];

    }


    /* end PutStore */
}
