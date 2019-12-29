<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Deadline;

use Slim\Http\Request;
use Slim\Http\Response;

class GetDeadline extends BaseDeadline
{


    public function __invoke(Request $request, Response $response, $args)
    {
        $sth = $this->container->db->prepare("SELECT * FROM `Deadlines` WHERE `DeadlineID` = '".$args['id']."'");
        $sth->execute();
        $deadlines = $sth->fetchAll();
        if (empty($deadlines)) {
            return $this->errorResponse($request, $response, 'Not Found', 'Deadline Not Found', 404);
        }
        if (\ciab\RBAC::havePermission('api.get.deadline.'.$deadlines[0]['DepartmentID'])) {
            return $this->jsonResponse($request, $response, $this->buildDeadline(
                $request,
                $response,
                $deadlines[0]['DeadlineID'],
                $deadlines[0]['DepartmentID'],
                $deadlines[0]['Deadline'],
                $deadlines[0]['Note']
            ));
        } else {
            return $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403);
        }

    }


    /* end GetDeadline */
}
