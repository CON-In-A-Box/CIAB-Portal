<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Deadline;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\NotFoundException;
use App\Controller\InvalidParameterException;

class PostDeadline extends BaseDeadline
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $department = $this->getDepartment($params['dept']);
        if ($department === null) {
            throw new NotFoundException("Department '${params['dept']}' Not Found");
        }
        $permissions = ['api.post.deadline.'.$department['id'],
        'api.post.deadline.all'];
        $this->checkPermissions($permissions);

        $body = $request->getParsedBody();
        if (!array_key_exists('Deadline', $body)) {
            throw new InvalidParameterException('Required \'Deadline\' parameter not present');
        }
        if (!array_key_exists('Note', $body)) {
            throw new InvalidParameterException('Required \'Note\' parameter not present');
        }
        $date = strtotime($body['Deadline']);
        if ($date == false) {
            throw new InvalidParameterException('\'Deadline\' parameter not valid \''.$body['Deadline'].'\'');
        }
        if ($date < strtotime('now')) {
            throw new InvalidParameterException('\'Deadline\' parameter in the past not valid \''.$body['Deadline'].'\'');
        }
        $sql_date = date("Y-m-d", $date);
        $sth = $this->container->db->prepare("INSERT INTO `Deadlines` (DepartmentID, Deadline, Note) VALUES ({$department['id']}, '$sql_date', '{$body['Note']}')");
        $sth->execute();
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        [null],
        201
        ];

    }


    /* end PostDeadline */
}
