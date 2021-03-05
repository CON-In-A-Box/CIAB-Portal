<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Announcement;

use Slim\Http\Request;
use Slim\Http\Response;

class ListDepartmentAnnouncements extends BaseAnnouncement
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $department = $this->getDepartment($args['name']);
        $sth = $this->container->db->prepare(
            "SELECT * FROM `Announcements` WHERE DepartmentID = '".$department['id']."' ORDER BY `PostedOn` ASC"
        );
        $sth->execute();
        $todos = $sth->fetchAll();
        $output = array();
        $output['type'] = 'announcement_list';
        $data = array();
        foreach ($todos as $entry) {
            $announce = new GetAnnouncement($this->container);
            $result = $this->buildAnnouncement(
                $request,
                $response,
                $entry['AnnouncementID'],
                $entry['DepartmentID'],
                $entry['PostedOn'],
                $entry['PostedBy'],
                $entry['Scope'],
                $entry['Text']
            );
            $data[] = $announce->arrayResponse($request, $response, $result);
        }
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        $output];

    }


    public function processIncludes(Request $request, Response $response, $args, $values, &$data)
    {
        $this->baseIncludes($request, $response, $args, $values, $data);

    }


    /* end ListDepartmentAnnouncements */
}
