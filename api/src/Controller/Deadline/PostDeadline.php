<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Deadline;

use Slim\Http\Request;
use Slim\Http\Response;

class PostDeadline extends BaseDeadline
{


    public function __invoke(Request $request, Response $response, $args)
    {
        $sth = $this->container->db->prepare("SELECT * FROM `Deadlines` WHERE `DeadlineID` = '".$args['id']."'");
        $sth->execute();
        $deadlines = $sth->fetchAll();
        if (empty($deadlines)) {
            return $this->errorResponse($request, $response, 'Not Found', 'Deadline Not Found', 404);
        }
        $target = $deadlines[0];

        $department = $target['DepartmentID'];
        if (!\ciab\RBAC::havePermission('api.post.deadline.'.$department)) {
            return $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403);
        }

        $body = $request->getParsedBody();

        if (array_key_exists('Department', $body)) {
            $department = $this->getDepartment($body['Department']);
            if ($department === null) {
                return $this->errorResponse(
                    $request,
                    $response,
                    'Not Found',
                    'Department \''.$body['Department'].'\' Not Found',
                    404
                );
            }
            $target['DepartmentID'] = $department['id'];
        }

        if (array_key_exists('Deadline', $body)) {
            $date = strtotime($body['Deadline']);
            if ($date == false) {
                return $this->errorResponse($request, $response, '\'Deadline\' parameter not valid \''.$body['Deadline'].'\'', 'Invalid Parameter', 400);
            }
            if ($date < strtotime('now')) {
                return $this->errorResponse($request, $response, '\'Deadline\' parameter in the past not valid \''.$body['Deadline'].'\'', 'Invalid Parameter', 400);
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
            WHERE `DeadlineID` = '{$args['id']}';
SQL
        );
        $sth->execute();
        return null;

    }


    /* end PostDeadline */
}
