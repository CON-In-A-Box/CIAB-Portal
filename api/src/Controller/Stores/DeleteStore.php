<?php declare(strict_types=1);

/**
 *  @OA\Delete(
 *      tags={"stores"},
 *      path="/stores/{id}",
 *      summary="Deletes a store",
 *      @OA\Parameter(
 *          description="Id of a store",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Response(
 *          response=204,
 *          description="OK"
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/store_not_found"
 *      ),
 *      security={{"ciab_auth": {}}}
 *  )
 */
namespace App\Controller\Stores;

use App\Controller\BaseController;
use App\Controller\PermissionDeniedException;
use App\Controller\NotFoundException;

use Atlas\Query\Delete;

use Slim\Http\Request;
use Slim\Http\Response;

class DeleteStore extends BaseStore
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        if (!$_SESSION['IS_ADMIN']) {
            throw new PermissionDeniedException();
        }

        $delete = Delete::new($this->container->db);
        $result = $delete->from('Stores')->whereEquals(['StoreID' => $params['id']])->perform();

        if ($result->rowCount() == 0) {
            throw new NotFoundException('Already deleted');
        }

        return [
        BaseController::RESOURCE_TYPE,
        [null],
        204
        ];

    }


  /* end DeleteStore */
}
