<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Artist;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\NotFoundException;
use Atlas\Query\Select;
use Atlas\Query\Delete;

class DeleteArtistDistribution extends BaseArtistDistribution
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $eid = $this->getEventId($request);
        $result = Select::new($this->container->db)
            ->columns('*')
            ->from('Artshow_Artist_Distribution')
            ->whereEquals(['DistributionID' => $params['id']])
            ->fetchAll();
        if (empty($result)) {
            throw new NotFoundException('Artshow Artist Distribution Not Found');
        }
        $target = $result[0];
        $this->checkDistributionPermission($request, $response, 'delete', $target['DistributionID']);

        Delete::new($this->container->db)
            ->from('Artshow_Artist_Distribution')
            ->whereEquals(['DistributionID' => $target['DistributionID']])
            ->perform();

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        [null],
        204
        ];

    }


    /* end DeleteArtistDistribution */
}
