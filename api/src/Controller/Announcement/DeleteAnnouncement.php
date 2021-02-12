<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Announcement;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\NotFoundException;

class DeleteAnnouncement extends BaseAnnouncement
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $target = $this->getAnnouncement($args['id']);
        $department = $target['DepartmentID'];
        if (!\ciab\RBAC::havePermission('api.delete.announcement.'.$department) &&
            !\ciab\RBAC::havePermission('api.delete.announcement.all')) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }

        $sth = $this->container->db->prepare(<<<SQL
            DELETE FROM `Announcements`
            WHERE `AnnouncementID` = '{$target['AnnouncementID']}';
SQL
        );
        $sth->execute();
        return [
        \App\Controller\BaseController::RESULT_TYPE,
        [null],
        204
        ];

    }


    /* end DeleteAnnouncement */
}
