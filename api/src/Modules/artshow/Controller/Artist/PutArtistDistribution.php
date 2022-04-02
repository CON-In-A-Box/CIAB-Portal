<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Artist;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Update;
use App\Controller\InvalidParameterException;
use App\Controller\PermissionDeniedException;

class PutArtistDistribution extends BaseArtistDistribution
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $this->checkPermissions(["api.update.artshow.distribution"]);
        $id = $params['id'];

        $body = $request->getParsedBody();
        if (!$body) {
            throw new InvalidParameterException("Body required");
        }

        Update::new($this->container->db)
            ->table('Artshow_Artist_Distribution')
            ->columns(BaseArtistDistribution::insertPayloadFromParams($body, false))
            ->whereEquals(['DistributionID' => $id])
            ->perform();

        $target = new GetArtistDistribution($this->container);
        $data = $target->buildResource($request, $response, ['id' => $id])[1];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data)
        ];

    }


    /* end PutDistribution */
}
