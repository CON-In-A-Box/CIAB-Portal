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
use Atlas\Query\Update;
use App\Error\InvalidParameterException;

class PutAnnouncement extends BaseAnnouncement
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $target = $this->getAnnouncement($params['id']);
        $department = $target['department'];

        $permissions = ['api.put.announcement.all',
        'api.put.announcement.'.$department];
        $this->checkPermissions($permissions);

        $body = $request->getParsedBody();
        if (empty($body)) {
            throw new InvalidParameterException('No update parameter present');
        }

        if (array_key_exists('department', $body)) {
            $department = $this->getDepartment($body['department'])['id'];
            $permissions = ['api.put.announcement.all',
            'api.put.announcement.'.$department];
            $this->checkPermissions($permissions);
            $body['department'] = $department;
        }

        $update = Update::new($this->container->db);
        $update->table('Announcements');
        $update->columns(BaseAnnouncement::insertPayloadFromParams($body, false));
        $update->whereEquals(['AnnouncementID' => $params['id']]);
        $update->perform();
        return [null];

    }


    /* end PutAnnouncement */
}
