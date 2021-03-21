<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Tag(
 *      name="announcements",
 *      description="Features around text announcements to event and event staff"
 *  )
 *
 *  @OA\Schema(
 *      schema="announcement",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"announcement"}
 *      ),
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          description="announcement ID"
 *      ),
 *      @OA\Property(
 *          property="postedOn",
 *          type="string",
 *          format="date",
 *          description="Date the announcement was first posted"
 *      ),
 *      @OA\Property(
 *          property="departmentId",
 *          description="Department for the announcement",
 *          oneOf={
 *              @OA\Schema(
 *                  type="integer",
 *                  description="Department Id"
 *              ),
 *              @OA\Schema(
 *                  ref="#/components/schemas/department"
 *              )
 *          }
 *      ),
 *      @OA\Property(
 *          property="postedBy",
 *          description="The member who created the announcement",
 *          oneOf={
 *              @OA\Schema(
 *                  type="integer",
 *                  description="Member Id"
 *              ),
 *              @OA\Schema(
 *                  ref="#/components/schemas/member"
 *              )
 *          }
 *      ),
 *      @OA\Property(
 *          property="scope",
 *          type="integer",
 *          description="The scope of the announcement"
 *      ),
 *      @OA\Property(
 *          property="text",
 *          type="string",
 *          description="Text of the announcement"
 *      )
 *  )
 *
 *  @OA\Schema(
 *      schema="announcement_list",
 *      allOf = {
 *          @OA\Schema(ref="#/components/schemas/resource_list")
 *      },
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"announcement_list"}
 *      ),
 *      @OA\Property(
 *          property="data",
 *          type="array",
 *          description="List of announcements",
 *          @OA\Items(
 *              ref="#/components/schemas/announcement"
 *          ),
 *      )
 *  )
 *
 *   @OA\Response(
 *      response="announce_not_found",
 *      description="Announcement not found in the system.",
 *      @OA\JsonContent(
 *          ref="#/components/schemas/error"
 *      )
 *   )
 **/

namespace App\Controller\Announcement;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\BaseController;
use App\Controller\NotFoundException;
use App\Controller\IncludeResource;

abstract class BaseAnnouncement extends BaseController
{


    public function __construct(Container $container)
    {
        parent::__construct('deadline', $container);
        \ciab\RBAC::customizeRBAC(array($this, 'customizeAnnouncementRBAC'));

        $this->includes = [
        new IncludeResource(
            '\App\Controller\Member\GetMember',
            'id',
            'postedBy'
        ),
        new IncludeResource(
            '\App\Controller\Department\GetDepartment',
            'name',
            'departmentId'
        )
        ];

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


    /* End BaseAnnouncement */
}
