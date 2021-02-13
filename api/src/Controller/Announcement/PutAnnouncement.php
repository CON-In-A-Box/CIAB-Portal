<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Announcement;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\NotFoundException;

class PutAnnouncement extends BaseAnnouncement
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $target = $this->getAnnouncement($args['id']);
        $department = $target['DepartmentID'];

        $permissions = ['api.put.announcement.all',
        'api.put.announcement.'.$department];
        $this->checkPermissions($permissions);

        $sql = "UPDATE `Announcements` SET ";
        $changes = [];

        $body = $request->getParsedBody();

        if (array_key_exists('Department', $body)) {
            $department = $this->getDepartment($body['Department']);
            if ($department === null) {
                throw new NotFoundException("Department '${body['Department']}' Not Found");
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
