<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Permissions;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class AnnouncementPermission extends BasePermission
{


    const ALL_METHODS = ['put', 'post', 'delete'];


    public function __construct(Container $container)
    {
        parent::__construct($container);
        $announce = new \App\Controller\Announcement\GetAnnouncement($container);
        \ciab\RBAC::customizeRBAC(array($announce, 'customizeAnnouncementRBAC'));

    }


    public function processIncludes(Request $request, Response $response, $args, $values, &$data)
    {
        if (in_array('departmentId', $values)) {
            $target = new \App\Controller\Department\GetDepartment($this->container);
            $newargs = $args;
            $newargs['name'] = $data['subdata']['departmentId'];
            $newdata = $target->buildResource($request, $response, $newargs)[1];
            if ($newdata['id'] != $data['id']) {
                $target->processIncludes($request, $response, $args, $values, $newdata);
                $data['subdata']['departmentId'] = $target->arrayResponse($request, $response, $newdata);
            }
        }

    }


    protected function buildDeptEntry($id, $allowed, $subtype, $method, $hateoas)
    {
        $entry = $this->buildBaseEntry($allowed, $subtype, $method, $hateoas);
        $entry['subdata'] = [
        'departmentId' => $id
        ];
        return $entry;

    }


    /* end AnnouncementPermission */
}
