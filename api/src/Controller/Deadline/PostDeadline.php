<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Deadline;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\NotFoundException;

class PostDeadline extends BaseDeadline
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $department = $this->getDepartment($params['dept']);
        if ($department === null) {
            throw new NotFoundException("Department '${params['dept']}' Not Found");
        }
        if (\ciab\RBAC::havePermission('api.post.deadline.'.$department['id']) ||
            \ciab\RBAC::havePermission('api.post.deadline.all')) {
            $body = $request->getParsedBody();
            if (!array_key_exists('Deadline', $body)) {
                return [
                \App\Controller\BaseController::RESULT_TYPE,
                $this->errorResponse($request, $response, 'Required \'Deadline\' parameter not present', 'Missing Parameter', 400)];
            }
            if (!array_key_exists('Note', $body)) {
                return [
                \App\Controller\BaseController::RESULT_TYPE,
                $this->errorResponse($request, $response, 'Required \'Note\' parameter not present', 'Missing Parameter', 400)];
            }
            $date = strtotime($body['Deadline']);
            if ($date == false) {
                return [
                \App\Controller\BaseController::RESULT_TYPE,
                $this->errorResponse($request, $response, '\'Deadline\' parameter not valid \''.$body['Deadline'].'\'', 'Invalid Parameter', 400)];
            }
            if ($date < strtotime('now')) {
                return [
                \App\Controller\BaseController::RESULT_TYPE,
                $this->errorResponse($request, $response, '\'Deadline\' parameter in the past not valid \''.$body['Deadline'].'\'', 'Invalid Parameter', 400)];
            }
            $sql_date = date("Y-m-d", $date);
            $sth = $this->container->db->prepare("INSERT INTO `Deadlines` (DepartmentID, Deadline, Note) VALUES ({$department['id']}, '$sql_date', '{$body['Note']}')");
            $sth->execute();
            return [null];
        } else {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }

    }


    /* end PostDeadline */
}
