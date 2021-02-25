<?php declare(strict_types=1);

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
