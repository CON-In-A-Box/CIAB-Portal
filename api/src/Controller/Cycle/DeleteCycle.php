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
        if (!\ciab\RBAC::havePermission('api.delete.cycle')) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }

        $sth = $this->container->db->prepare("DELETE FROM `AnnualCycles` WHERE `AnnualCycleID` = ".$params['id'].";");
        $sth->execute();
        return [null];

    }


    /* end DeleteCycle */
}
