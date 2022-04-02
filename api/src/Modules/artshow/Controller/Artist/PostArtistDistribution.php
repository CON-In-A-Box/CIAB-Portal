<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Artist;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\IncludeResource;
use App\Controller\PermissionDeniedException;
use Atlas\Query\Insert;

class PostArtistDistribution extends BaseArtistDistribution
{


    public function __construct($container)
    {
        parent::__construct($container);
        $this->includes = [
        new IncludeResource('\App\Modules\artshow\Controller\Artist\GetArtist', 'artist', 'artist'),
        new IncludeResource('\App\Controller\Event\GetEvent', 'id', 'event')
        ];

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $this->checkPermission(["api.post.artshow.distribution"]);
        $eid = $this->getEventId($request);

        $fields = array();
        $body = $request->getParsedBody();

        if (!array_key_exists('event', $body)) {
            $body['event'] = $eid;
        }
        $body['artist'] = $params['artist'];

        $insert = Insert::new($this->container->db)
            ->into('Artshow_Artist_Distribution')
            ->columns(BaseArtistDistribution::insertPayloadFromParams($body));
        $insert->perform();
        $id = $insert->getLastInsertId();

        $target = new GetArtistDistribution($this->container);
        $data = $target->buildResource($request, $response, ['id' => $id])[1];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data),
        201
        ];

    }


    /* end PostArtistDistribution */
}
