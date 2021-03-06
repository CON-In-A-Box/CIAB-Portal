<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"announcements"},
 *      path="/announcement/{id}",
 *      summary="Gets an announcement",
 *      @OA\Parameter(
 *          description="Id of the announcement",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
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
 *           )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Announcement found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/announcement"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/announce_not_found"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

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
