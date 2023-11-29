<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/
/**
 *  @OA\Delete(
 *      tags={"announcements"},
 *      path="/announcement/{id}",
 *      summary="Deletes an announcement",
 *      @OA\Parameter(
 *          description="Id of the announcement",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Response(
 *          response=204,
 *          description="OK"
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/announce_not_found"
 *      ),
 *      security={
 *          {"ciab_auth": {}}
 *       }
 *  )
 **/

namespace App\Controller\Announcement;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Delete;
use App\Error\NotFoundException;

class DeleteAnnouncement extends BaseAnnouncement
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $target = $this->getAnnouncement($args['id']);
        $permissions = ['api.delete.announcement.all',
        'api.delete.announcement.'.$target['department']];
        $this->checkPermissions($permissions);
        $delete = Delete::new($this->container->db);
        $delete->from('Announcements')->whereEquals(['AnnouncementID' => $target['id']])->perform();
        return [
        \App\Controller\BaseController::RESULT_TYPE,
        [null],
        204
        ];

    }


    /* end DeleteAnnouncement */
}
