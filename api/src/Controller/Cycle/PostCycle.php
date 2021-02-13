<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Cycle;

use Slim\Http\Request;
use Slim\Http\Response;

class PostCycle extends BaseCycle
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $permissions = ['api.post.cycle'];
        $this->checkPermissions($permissions);

        $body = $request->getParsedBody();
        if (!array_key_exists('From', $body)) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Required \'From\' parameter not present', 'Missing Parameter', 400)];
        }
        if (!array_key_exists('To', $body)) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Required \'To\' parameter not present', 'Missing Parameter', 400)];
        }
        $from = date_format(new \DateTime($body['From']), 'Y-m-d');
        $to = date_format(new \DateTime($body['To']), 'Y-m-d');
        $sql = "INSERT INTO `AnnualCycles` (`AnnualCycleID`, `DateFrom`, `DateTo`) VALUES (NULL, '$from', '$to')";
        $sth = $this->container->db->prepare($sql);
        $sth->execute();

        $target = new \App\Controller\Cycle\GetCycle($this->container);
        $data = $target->buildResource($request, $response, ['id' => $this->container->db->lastInsertId()])[1];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data)
        ];

    }


    /* end PostCycle */
}
