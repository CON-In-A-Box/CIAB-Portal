<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\concom\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\NotFoundException;

class DeleteStaffMembership extends BaseConcom
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $sth = $this->container->db->prepare("SELECT * FROM `ConComList` WHERE `ListRecordID` = '".$params['id']."'");
        $sth->execute();
        $record = $sth->fetchAll();
        if (empty($record)) {
            throw new NotFoundException('Staff Membership  Record Not Found');
        }
        $target = $record[0];

        $permissions = ['api.delete.concom.all',
        'api.delete.concom.'.$target['DepartmentID']];
        $this->checkPermissions($permissions);

        $sth = $this->container->db->prepare(<<<SQL
            DELETE FROM `ConComList`
            WHERE `ListRecordID` = '{$params['id']}';
SQL
        );
        $sth->execute();
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        [null],
        204
        ];

    }


    /* end DeleteConCom */
}
