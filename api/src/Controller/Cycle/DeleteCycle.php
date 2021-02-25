<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Cycle;

use Slim\Http\Request;
use Slim\Http\Response;

class DeleteCycle extends BaseCycle
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $this->getCycle($params);
        $permissions = ['api.delete.cycle'];
        $this->checkPermissions($permissions);

        $sth = $this->container->db->prepare("DELETE FROM `AnnualCycles` WHERE `AnnualCycleID` = ".$params['id'].";");
        $sth->execute();
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        [null],
        204
        ];

    }


    /* end DeleteCycle */
}
