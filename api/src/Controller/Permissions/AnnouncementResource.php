<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Permissions;

use Slim\Http\Request;
use Slim\Http\Response;

use App\Controller\NotFoundException;

class AnnouncementResource extends AnnouncementPermission
{


    private function buildEntry($request, $id, $method): array
    {
        $path = $request->getUri()->getBaseUrl();
        $allowed = (\ciab\RBAC::havePermission('api.'.$method.'.announcement.'.$id) ||
                    \ciab\RBAC::havePermission('api.'.$method.'.announcement.all'));
        ;
        return $this->buildDeptEntry(
            $id,
            $allowed,
            'announcement',
            $method,
            [
            'method' => $method,
            'href' => $path.'/announcement/'.$id,
            'request' => strtoupper($method)
            ]
        );

    }


    public function buildResource(Request $request, Response $response, $args): array
    {
        global $Departments;

        $path = $request->getUri()->getBaseUrl();
        $result = array();
        $data = $this->getDepartment($args['department']);
        if ($data === null) {
            throw new NotFoundException('Department \''.$args['department'].'\' Invalid');
        }
        $id = $data['id'];
        $method = $args['method'];
        if ($method !== null && !in_array($method, AnnouncementPermission::ALL_METHODS)) {
            throw new NotFoundException('Method \'announcement.'.$method.'\' Invalid');
        }
        if ($method !== null) {
            $result[] = $this->buildEntry($request, $id, $method);
        } else {
            foreach (AnnouncementPermission::ALL_METHODS as $method) {
                $result[] = $this->buildEntry($request, $id, $method);
            }
        }
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $result,
        array('type' => 'permission_list')];

    }


    /* end AnnouncementResource */
}
