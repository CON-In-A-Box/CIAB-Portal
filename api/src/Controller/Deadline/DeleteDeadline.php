<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Deadline;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\NotFoundException;

class DeleteDeadline extends BaseDeadline
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $sth = $this->container->db->prepare("SELECT * FROM `Deadlines` WHERE `DeadlineID` = '".$args['id']."'");
        $sth->execute();
        $deadlines = $sth->fetchAll();
        if (empty($deadlines)) {
            throw new NotFoundException('Deadline Not Found');
        }
        $target = $deadlines[0];

        $permissions = ['api.delete.deadline.all',
        'api.delete.deadline.'.$target['DepartmentID']];
        $this->checkPermissions($permissions);

        $sth = $this->container->db->prepare(<<<SQL
            DELETE FROM `Deadlines`
            WHERE `DeadlineID` = '{$target['DeadlineID']}';
SQL
        );
        $sth->execute();
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        [null],
        204
        ];

    }


    /* end DeleteDeadline */
}
