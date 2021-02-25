<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Deadline;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\NotFoundException;

class GetDeadline extends BaseDeadline
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $sth = $this->container->db->prepare("SELECT * FROM `Deadlines` WHERE `DeadlineID` = '".$args['id']."'");
        $sth->execute();
        $deadlines = $sth->fetchAll();
        if (empty($deadlines)) {
            throw new NotFoundException('Deadline Not Found');
        }
        $permissions = ['api.get.deadline.'.$deadlines[0]['DepartmentID'],
        'api.get.deadline.all'];
        $this->checkPermissions($permissions);
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
