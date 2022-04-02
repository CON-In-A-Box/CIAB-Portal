<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Artist;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\IncludeResource;
use App\Controller\NotFoundException;
use Atlas\Query\Select;

class ListArtistDistribution extends BaseArtistDistribution
{


    public function __construct($container)
    {
        parent::__construct($container);
        $this->includes = [
        new IncludeResource('\App\Modules\artshow\Controller\Artist\GetArtist', 'artist', 'artist'),
        new IncludeResource('\App\Controller\Event\GetEvent', 'id', 'event'),
        ];

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $eid = $this->getEventId($request);

        $data = Select::new($this->container->db)
            ->columns(...BaseArtistDistribution::selectMapping())
            ->from('Artshow_Artist_Distribution')
            ->whereEquals(['ArtistID' => $params['artist'], 'EventID' => $eid])
            ->fetchAll();
        if (empty($data) && empty($data2)) {
            throw new NotFoundException('Distribution Not Found');
        }
        $output = array();
        $output['type'] = 'artshow_artist_distribution_list';
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        $output];

    }


    /* end ListArtistDistribution */
}
