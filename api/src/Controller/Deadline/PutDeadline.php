<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Deadline;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\NotFoundException;

class PutDeadline extends BaseDeadline
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $sth = $this->container->db->prepare("SELECT * FROM `Deadlines` WHERE `DeadlineID` = '".$params['id']."'");
        $sth->execute();
        $deadlines = $sth->fetchAll();
        if (empty($deadlines)) {
            throw new NotFoundException('Deadline Not Found');
        }
        $target = $deadlines[0];

        $department = $target['DepartmentID'];
        if (!\ciab\RBAC::havePermission('api.put.deadline.'.$department) &&
            !\ciab\RBAC::havePermission('api.put.deadline.all')) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }

        $body = $request->getParsedBody();

        if (array_key_exists('Department', $body)) {
            $department = $this->getDepartment($body['Department']);
            if ($department === null) {
                throw new NotFoundException("Department '${body['Department']}' Not Found");
            }
            $target['DepartmentID'] = $department['id'];
        }

        if (array_key_exists('Deadline', $body)) {
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
            $target['Deadline'] = date("Y-m-d", $date);
        }
        if (array_key_exists('Note', $body)) {
            $target['Note'] = $body['Note'];
        }

        $sth = $this->container->db->prepare(<<<SQL
            UPDATE `Deadlines`
            SET
                `DepartmentID` = {$target['DepartmentID']},
                `Deadline` = '{$target['Deadline']}',
                `Note` = '{$target['Note']}'
            WHERE `DeadlineID` = '{$params['id']}';
SQL
        );
        $sth->execute();
        return [null];

    }


    /* end PutDeadline */
}
