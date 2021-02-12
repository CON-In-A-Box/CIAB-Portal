<?php declare(strict_types=1);

namespace App\Controller\Stores;

use App\Controller\BaseController;
use App\Controller\PermissionDeniedException;

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
            return [
            BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Already deleted', 'Not Found', 404)
            ];
        }

        return [
        BaseController::RESOURCE_TYPE,
        [null],
        204
        ];

    }


  /* end DeleteStore */
}
