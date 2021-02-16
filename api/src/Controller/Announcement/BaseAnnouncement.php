<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Announcement;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\BaseController;
use App\Controller\NotFoundException;

abstract class BaseAnnouncement extends BaseController
{


    public function __construct(Container $container)
    {
        parent::__construct('deadline', $container);
        \ciab\RBAC::customizeRBAC(array($this, 'customizeAnnouncementRBAC'));

    }


    public function getAnnouncement($id)
    {
        $sth = $this->container->db->prepare("SELECT * FROM `Announcements` WHERE `AnnouncementID` = $id");
        $sth->execute();
        $announce = $sth->fetchAll();
        if (empty($announce)) {
            throw new NotFoundException('Announcement Not Found');
        }
        return $announce[0];

    }


    public function buildAnnouncement(Request $request, Response $response, $id, $dept, $posted, $poster, $scope, $text)
    {
        $this->buildAnnouncementHateoas($request, intval($id), intval($dept));
        $output = array();
        $output['type'] = 'announcement';
        $output['id'] = $id;
        $output['departmentId'] = $dept;
        $output['postedOn'] = $posted;
        $output['postedBy'] = $poster;
        $output['scope'] = $scope;
        $output['text'] = $text;
        return $output;

    }


    public function customizeAnnouncementRBAC($instance)
    {
        $positions = [];
        $sql = "SELECT `PositionID`, `Name` FROM `ConComPositions` ORDER BY `PositionID` ASC";
        $result = $this->container->db->prepare($sql);
        $result->execute();
        $value = $result->fetch();
        while ($value !== false) {
            $positions[intval($value['PositionID'])] = $value['Name'];
            $value = $result->fetch();
        }

        $result = $this->container->db->prepare("SELECT `DepartmentID` FROM `Departments`");
        $result->execute();
        $value = $result->fetch();
        while ($value !== false) {
            $perm_del = 'api.delete.announcement.'.$value['DepartmentID'];
            $perm_pos = 'api.post.announcement.'.$value['DepartmentID'];
            $perm_put = 'api.put.announcement.'.$value['DepartmentID'];
            $target_h = $value['DepartmentID'].'.'.array_keys($positions)[0];
            try {
                $role = $instance->getRole($target_h);
                $role->addPermission($perm_del);
                $role->addPermission($perm_pos);
                $role->addPermission($perm_put);
            } catch (Exception\InvalidArgumentException $e) {
                error_log($e);
            }
            $value = $result->fetch();
        }

    }


    protected function buildAnnouncementHateoas(Request $request, int $id, int $dept)
    {
        if ($id !== 0) {
            $path = $request->getUri()->getBaseUrl();
            $this->addHateoasLink('self', $path.'/announcement/'.strval($id), 'GET');
            $this->addHateoasLink('modify', $path.'/announcement/'.strval($id), 'POST');
            $this->addHateoasLink('delete', $path.'/announcement/'.strval($id), 'DELETE');
            $this->addHateoasLink('department', $path.'/announcement/'.strval($dept), 'GET');
        }

    }


    protected function baseIncludes(Request $request, Response $response, $args, $values, &$data)
    {
        if (in_array('departmentId', $values)) {
            $target = new \App\Controller\Department\GetDepartment($this->container);
            $newargs = $args;
            $newargs['name'] = $data['departmentId'];
            $newdata = $target->buildResource($request, $response, $newargs)[1];
            $target->processIncludes($request, $response, $args, $values, $newdata);
            $data['departmentId'] = $target->arrayResponse($request, $response, $newdata);
        }
        if (in_array('postedBy', $values)) {
            $target = new \App\Controller\Member\GetMember($this->container);
            $newargs = $args;
            $newargs['name'] = $data['postedBy'];
            $newdata = $target->buildResource($request, $response, $newargs)[1];
            $target->processIncludes($request, $response, $args, $values, $newdata);
            $data['postedBy'] = $target->arrayResponse($request, $response, $newdata);
        }

    }


    /* End BaseAnnouncement */
}
