<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 **/

namespace App\Modules\artshow\Controller\Artist;

use Slim\Container;
use App\Controller\BaseController;
use App\Modules\artshow\Controller\BaseArtshow;

abstract class BaseArtistDistribution extends BaseArtshow
{

    protected static $columnsToAttributes = [
    '"artist_distribution"' => 'type',
    'DistributionID' => 'id',
    'ArtistID' => 'artist',
    'EventID' => 'event',
    'Date' => 'date',
    'CheckNumber' => 'check_number',
    'Amount' => 'amount'
    ];


    public function __construct(Container $container)
    {
        parent::__construct('artist_distribution', $container);

    }


    /* End BaseArtistDistribution */
}
