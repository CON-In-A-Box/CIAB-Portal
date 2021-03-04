<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Put(
 *      tags={"announcements"},
 *      path="/announcement/{id}",
 *      summary="Updates a announcement",
 *      @OA\Parameter(
 *          description="Id of the announcement",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="Department",
 *                      type="integer",
 *                      nullable=true
 *                  ),
 *                  @OA\Property(
 *                      property="Text",
 *                      type="string",
 *                      nullable=true
 *                  ),
 *                  @OA\Property(
 *                      property="Scope",
 *                      type="integer",
 *                      nullable=true
 *                  )
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="OK"
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
use App\Controller\NotFoundException;
use App\Controller\InvalidParameterException;

class PutAnnouncement extends BaseAnnouncement
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $target = $this->getAnnouncement($args['id']);
        $department = $target['DepartmentID'];

        $permissions = ['api.put.announcement.all',
        'api.put.announcement.'.$department];
        $this->checkPermissions($permissions);

        $sql = "UPDATE `Announcements` SET ";
        $changes = [];

        $body = $request->getParsedBody();

        if (empty($body)) {
            throw new InvalidParameterException('No update parameter present');
        }

        if (array_key_exists('Department', $body)) {
            $department = $this->getDepartment($body['Department']);
            $changes[] = "`DepartmentID` = '{$department['id']}' ";
        }

        if (array_key_exists('Text', $body)) {
            $text = \MyPDO::quote($body['Text']);
            $changes[] = "`Text` = $text ";
        }

        if (array_key_exists('Scope', $body)) {
            $changes[] = "`Scope` = '{$body['Scope']}' ";
        }

        if (count($changes) > 0) {
            $sql .= implode(',', $changes);
            $sql .= "WHERE `AnnouncementID` = '{$args['id']}';";
            $sth = $this->container->db->prepare($sql);
            $sth->execute();
        }
        return [null];

    }


    /* end PutAnnouncement */
}
