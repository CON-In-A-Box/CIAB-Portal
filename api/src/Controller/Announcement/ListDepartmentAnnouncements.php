<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"departments"},
 *      path="/department/{id}/announcements",
 *      summary="Lists announcements for a given department",
 *      @OA\Parameter(
 *          description="The id or name of the department",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(
 *              oneOf = {
 *                  @OA\Schema(
 *                      description="Department name",
 *                      type="integer"
 *                  ),
 *                  @OA\Schema(
 *                      description="Department id",
 *                      type="string"
 *                  )
 *              }
 *          )
 *      ),
 *      @OA\Parameter(
 *          description="Include the resource instead of the ID.",
 *          in="query",
 *          name="include",
 *          required=false,
 *          explode=false,
 *          style="form",
 *          @OA\Schema(
 *              type="array",
 *              @OA\Items(
 *                  type="string",
 *                  enum={"departmentId","postedBy"}
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="OK",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/announcement_list"
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/department_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

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


    /* end ListDepartmentAnnouncements */
}
