<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Configuration;

use Slim\Http\Request;
use Slim\Http\Response;

class DeleteRegistrationQuestion extends BaseConfiguration
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        return $this->deleteConfigValue($params['id'], 'Artshow_RegistrationQuestion', 'QuestionID');

    }


    /* end DeleteRegistrationQuestion */
}
