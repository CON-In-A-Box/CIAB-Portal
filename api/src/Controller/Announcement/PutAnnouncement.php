<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Announcement;

use Slim\Http\Request;
use Slim\Http\Response;

class PutAnnouncement extends BaseAnnouncement
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $sth = $this->container->db->prepare("SELECT * FROM `Announcements` WHERE `AnnouncementID` = ".$args['id']);
        $sth->execute();
        $announce = $sth->fetchAll();
        if (empty($announce)) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Not Found', 'Announcement Not Found', 404)];
        }
        $target = $announce[0];

        $department = $target['DepartmentID'];
        if (!\ciab\RBAC::havePermission('api.put.announcement.'.$department) &&
            !\ciab\RBAC::havePermission('api.put.announcement.all')) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }

        $sql = "UPDATE `Announcements` SET ";
        $changes = [];

        $body = $request->getParsedBody();

        if (array_key_exists('Department', $body)) {
            $department = $this->getDepartment($body['Department']);
            if ($department === null) {
                return [
                \App\Controller\BaseController::RESULT_TYPE,
                $this->errorResponse(
                    $request,
                    $response,
                    'Not Found',
                    'Department \''.$body['Department'].'\' Not Found',
                    404
                )];
            }
            $changes[] = "`DepartmentID` = '{$department['id']}' ";
        }

        if (array_key_exists('Text', $body)) {
            $text = \MyPDO::quote($body['Text']);
            $changes[] = "`Text` = $text ";
        }

        if (array_key_exists('Scope', $body)) {
            $changes[] = "`Scope` = '{$body['Scope']}' ";
        }

        if (count($changes) > 0) {
            $sql .= implode(',', $changes);
            $sql .= "WHERE `AnnouncementID` = '{$args['id']}';";
            $sth = $this->container->db->prepare($sql);
            $sth->execute();
        }
        return [null];

    }


    /* end PutAnnouncement */
}
