<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Announcement;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\NotFoundException;

class GetAnnouncement extends BaseAnnouncement
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $sth = $this->container->db->prepare("SELECT * FROM `Announcements` WHERE `AnnouncementID` = '".$args['id']."'");
        $sth->execute();
        $announce = $sth->fetchAll();
        if (empty($announce)) {
            throw new NotFoundException('Announcement Not Found');
        }
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $this->buildAnnouncement(
            $request,
            $response,
            $announce[0]['AnnouncementID'],
            $announce[0]['DepartmentID'],
            $announce[0]['PostedOn'],
            $announce[0]['PostedBy'],
            $announce[0]['Scope'],
            $announce[0]['Text']
        )];

    }


    public function processIncludes(Request $request, Response $response, $args, $values, &$data)
    {
        $this->baseIncludes($request, $response, $args, $values, $data);

    }


    /* end GetAnnouncement */
}
