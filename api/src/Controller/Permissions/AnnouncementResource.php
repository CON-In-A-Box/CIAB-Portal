<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Permissions;

use Slim\Http\Request;
use Slim\Http\Response;

class AnnouncementResource extends AnnouncementPermission
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        return $this->buildBaseResource($request, $response, $params);

    }


    /* end AnnouncementResource */
}
