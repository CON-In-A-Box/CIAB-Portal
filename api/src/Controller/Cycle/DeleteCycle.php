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
        $error = $this->getCycle($cycles, $params);
        if ($error) {
            return $error;
        }
        $permissions = ['api.delete.cycle'];
        $this->checkPermissions($permissions);

        $sth = $this->container->db->prepare("DELETE FROM `AnnualCycles` WHERE `AnnualCycleID` = ".$params['id'].";");
        $sth->execute();
        return [null];

    }


    /* end DeleteCycle */
}
