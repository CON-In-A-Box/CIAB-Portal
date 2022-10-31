<?php declare(strict_types=1);

/**
 *  @OA\Post(
 *      tags={"stores"},
 *      path="/stores",
 *      summary="Adds a new store",
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="application/json",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="store_slug",
 *                      type="string",
 *                      nullable=false
 *                  ),
 *                  @OA\Property(
 *                      property="name",
 *                      type="string",
 *                      nullable=false
 *                  ),
 *                  @OA\Property(
 *                      property="description",
 *                      type="string"
 *                  )
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=201,
 *          description="OK"
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Invalid parameters",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/error"
 *          )
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 */
namespace App\Controller\Stores;

use Atlas\Query\Insert;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\PermissionDeniedException;
use App\Controller\InvalidParameterException;

class PostStore extends BaseStore
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        // TODO: RBAC more than just admin maybe
        if (!$_SESSION['IS_ADMIN']) {
            throw new PermissionDeniedException();
        }

        $required_params = ['name', 'store_slug'];
        $body = $this->checkRequiredBody($request, $required_params);
        $insert = Insert::new($this->container->db);
        $insert->into('Stores')->columns(BaseStore::insertPayloadFromParams($body));

        $sth = $insert->perform();
        $id = $insert->getLastInsertId();

        $store = $this->getStore(array('id' => $id), $request, $response, $error);

        $output = array(
            'type' => 'store',
            'data' => $store
        );

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $output,
        201
        ];

    }


    /* end PostStore */
}
