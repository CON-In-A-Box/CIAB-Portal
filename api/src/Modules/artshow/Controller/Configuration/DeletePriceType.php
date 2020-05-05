<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Configuration;

use Slim\Http\Request;
use Slim\Http\Response;

class DeletePriceType extends BaseConfiguration
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        return $this->deleteConfigValue($params['type'], 'Artshow_PriceType', 'PriceType');

    }


    /* end DeletePriceType */
}
