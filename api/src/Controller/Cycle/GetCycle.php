<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Cycle;

use Slim\Http\Request;
use Slim\Http\Response;

class GetCycle extends BaseCycle
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $sth = $this->container->db->prepare("SELECT * FROM `AnnualCycles` WHERE `AnnualCycleID` = ".$args['id']);
        $sth->execute();
        $cycles = $sth->fetchAll();
        if (empty($cycles)) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Not Found', 'Cycle Not Found', 404)];
        }
        $this->id = $args['id'];
        $this->buildCycleHateoas($request);
        $entry = $cycles[0];
        $entry['id'] = $this->id;
        unset($entry['AnnualCycleID']);
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $entry
        ];

    }


    /* end GetCycle */
}
