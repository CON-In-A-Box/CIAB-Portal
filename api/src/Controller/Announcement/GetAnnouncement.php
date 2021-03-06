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


    public function buildResource(Request $request, Response $response, $params): array
    {
        $target = $this->getAnnouncement($params['id']);
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $this->buildAnnouncement(
            $request,
            $response,
            $target['AnnouncementID'],
            $target['DepartmentID'],
            $target['PostedOn'],
            $target['PostedBy'],
            $target['Scope'],
            $target['Text']
        )];

    }


    /* end GetAnnouncement */
}
