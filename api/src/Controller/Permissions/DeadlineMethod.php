<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Permissions;

use Slim\Http\Request;
use Slim\Http\Response;

class DeadlineMethod extends DeadlinePermission
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        return $this->baseMethodResource($request, $response, $params);

    }


    /* end DeadlineMethod */
}
