<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"volunteers"},
 *      path="/member/{id}/volunteer/hours",
 *      summary="Gets member volunteer data",
 *      @OA\Parameter(
 *          description="Id of the member.",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/event"
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response"
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Member volunteer data found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/volunteer_hour_entry_list"
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

namespace App\Modules\volunteers\Controller\Hours;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\PermissionDeniedException;
use App\Controller\NotFoundException;
use Atlas\Query\Select;
use App\Controller\BaseController;
use \App\Controller\IncludeResource;

class GetMemberHours extends BaseHours
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $user = $request->getAttribute('oauth2-token')['user_id'];
        $event = $this->getEventId($request);
        if (array_key_exists('id', $params)) {
            $id = $this->getMember($request, $params['id'], null, false)[0]['id'];
        }
        if ($id == null) {
            $id = $user;
        }

        if ($id !== $user) {
            $permissions = ['api.get.volunteer.hours'];
            $this->checkPermissions($permissions);
        }

        $data = Select::new($this->container->db)
        ->columns(...BaseHours::selectMapping())
        ->from('VolunteerHours')
        ->whereEquals(['AccountID' => $id, 'EventID' => $event])
        ->fetchAll();

        if (empty($data)) {
            throw new NotFoundException('Volunteer records not found');
        }

        $sum = 0;
        foreach ($data as $entry) {
            $sum += $entry['hours'] * $entry['modifier'];
        }

        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        array('type' => 'volunteer_hour_entry_list', 'total_hours' => $sum)];

    }


    /* end GetMemberHours */
}
