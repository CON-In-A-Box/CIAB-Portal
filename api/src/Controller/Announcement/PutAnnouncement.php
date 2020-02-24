<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Announcement;

use Slim\Http\Request;
use Slim\Http\Response;

require_once __DIR__.'/../../../../functions/users.inc';

class PutAnnouncement extends BaseAnnouncement
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $department = $this->getDepartment($args['dept']);
        if ($department === null) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse(
                $request,
                $response,
                'Not Found',
                'Department \''.$args['dept'].'\' Not Found',
                404
            )];
        }
        if (\ciab\RBAC::havePermission('api.put.announcement.'.$department['id']) ||
            \ciab\RBAC::havePermission('api.put.announcement.all')) {
            $body = $request->getParsedBody();
            if (!array_key_exists('Scope', $body)) {
                return [
                \App\Controller\BaseController::RESULT_TYPE,
                $this->errorResponse($request, $response, 'Required \'Scope\' parameter not present', 'Missing Parameter', 400)];
            }
            if (!array_key_exists('Text', $body)) {
                return [
                \App\Controller\BaseController::RESULT_TYPE,
                $this->errorResponse($request, $response, 'Required \'Text\' parameter not present', 'Missing Parameter', 400)];
            }

            $user = $this->findMember($request, $response, null, null);
            $member = $user['Id'];

            $sth = $this->container->db->prepare("INSERT INTO `Announcements` (DepartmentID, PostedBy, PostedOn, Scope, Text) VALUES ({$department['id']}, $member, now(), '{$body['Scope']}', '{$body['Text']}')");
            $sth->execute();
            return [null];
        } else {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }

    }


    /* end PutAnnouncement */
}
