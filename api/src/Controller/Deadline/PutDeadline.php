<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Deadline;

use Slim\Http\Request;
use Slim\Http\Response;

class PutDeadline extends BaseDeadline
{


    public function __invoke(Request $request, Response $response, $args)
    {
        $department = $this->getDepartment($args['dept']);
        if ($department === null) {
            return $this->errorResponse(
                $request,
                $response,
                'Not Found',
                'Department \''.$args['dept'].'\' Not Found',
                404
            );
        }
        if (\ciab\RBAC::havePermission('api.put.deadline.'.$department['id'])) {
            $body = $request->getParsedBody();
            if (!array_key_exists('Deadline', $body)) {
                return $this->errorResponse($request, $response, 'Required \'Deadline\' parameter not present', 'Missing Parameter', 400);
            }
            if (!array_key_exists('Note', $body)) {
                return $this->errorResponse($request, $response, 'Required \'Note\' parameter not present', 'Missing Parameter', 400);
            }
            $date = strtotime($body['Deadline']);
            if ($date == false) {
                return $this->errorResponse($request, $response, '\'Deadline\' parameter not valid \''.$body['Deadline'].'\'', 'Invalid Parameter', 400);
            }
            if ($date < strtotime('now')) {
                return $this->errorResponse($request, $response, '\'Deadline\' parameter in the past not valid \''.$body['Deadline'].'\'', 'Invalid Parameter', 400);
            }
            $sql_date = date("Y-m-d", $date);
            $sth = $this->container->db->prepare("INSERT INTO `Deadlines` (DepartmentID, Deadline, Note) VALUES ({$department['id']}, '$sql_date', '{$body['Note']}')");
            $sth->execute();
            return null;
        } else {
            return $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403);
        }

    }


    /* end PutDeadline */
}
