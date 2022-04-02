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

class GetArtistDistribution extends BaseArtistDistribution
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
        $data = Select::new($this->container->db)
            ->columns(...BaseArtistDistribution::selectMapping())
            ->from('Artshow_Artist_Distribution')
            ->whereEquals(['DistributionID' => $params['id']])
            ->fetchOne();
        if (empty($data)) {
            throw new NotFoundException('Distribution Not Found');
        }
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $data];

    }


    /* end GetArtistDistribution */
}
