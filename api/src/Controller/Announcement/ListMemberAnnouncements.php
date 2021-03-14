<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Get(
 *      tags={"members"},
 *      path="/member/{id}/announcements",
 *      summary="Lists announcements for a given member",
 *      @OA\Parameter(
 *          description="The id or login of the member",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(
 *              oneOf = {
 *                  @OA\Schema(
 *                      description="Member login",
 *                      type="string"
 *                  ),
 *                  @OA\Schema(
 *                      description="Member id",
 *                      type="integer"
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
 *      @OA\Parameter(
 *          ref="#/components/parameters/maxResults",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/pageToken",
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
 *          ref="#/components/responses/member_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Controller\Announcement;

use Slim\Http\Request;
use Slim\Http\Response;

class ListMemberAnnouncements extends BaseAnnouncement
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $user = $this->findMemberId($request, $response, $args, 'id');
        $user = $user['id'];
        $sth = $this->container->db->prepare(<<<SQL
            SELECT
                *
            FROM
                `Announcements`
            WHERE
                `Scope` = 0 OR
                `Scope` = 1 AND (
                    SELECT
                        COUNT(AccountID)
                    FROM
                        `ConComList`
                    WHERE
                        `AccountID`  = '$user'
                ) > 0 OR
                `Scope` = 2 AND (
                `DepartmentID` IN(
                SELECT
                    `DepartmentID`
                FROM
                    `ConComList`
                WHERE
                    `AccountID` = '$user'
            ) OR `DepartmentID` IN(
                SELECT
                    `DepartmentID`
                FROM
                    `Departments`
                WHERE
                    `ParentDepartmentID` IN(
                    SELECT
                        `DepartmentID`
                    FROM
                        `ConComList`
                    WHERE
                        `AccountID` = '$user'
                )
            ))
            ORDER BY `PostedOn` ASC
SQL
        );
        $sth->execute();
        $todos = $sth->fetchAll();
        $output = array();
        $output['type'] = 'announce_list';
        $data = array();
        foreach ($todos as $entry) {
            $announce = new \App\Controller\Announcement\GetAnnouncement($this->container);
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


    /* end ListMemberAnnouncements */
}
