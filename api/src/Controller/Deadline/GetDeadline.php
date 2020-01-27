<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Deadline;

use Slim\Http\Request;
use Slim\Http\Response;

class GetDeadline extends BaseDeadline
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $sth = $this->container->db->prepare("SELECT * FROM `Deadlines` WHERE `DeadlineID` = '".$args['id']."'");
        $sth->execute();
        $deadlines = $sth->fetchAll();
        if (empty($deadlines)) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Not Found', 'Deadline Not Found', 404)];
        }
        if (\ciab\RBAC::havePermission('api.get.deadline.'.$deadlines[0]['DepartmentID'])) {
            return [
            \App\Controller\BaseController::RESOURCE_TYPE,
            $this->buildDeadline(
                $request,
                $response,
                $deadlines[0]['DeadlineID'],
                $deadlines[0]['DepartmentID'],
                $deadlines[0]['Deadline'],
                $deadlines[0]['Note']
            )];
        } else {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }

    }


    public function processIncludes(Request $request, Response $response, $args, $values, &$data)
    {
        if (in_array('departmentId', $values)) {
            $target = new \App\Controller\Department\GetDepartment($this->container);
            $newargs = $args;
            $newargs['name'] = $data['departmentId'];
            $newdata = $target->buildResource($request, $response, $newargs)[1];
            $target->processIncludes($request, $response, $args, $values, $newdata);
            $data['departmentId'] = $target->arrayResponse($request, $response, $newdata);
        }

    }


    /* end GetDeadline */
}
