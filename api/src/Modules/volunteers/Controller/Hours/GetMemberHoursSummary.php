<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"volunteers"},
 *      path="/member/{id}/volunteer/hours/summary",
 *      summary="Gets member volunteer summary",
 *      @OA\Parameter(
 *          description="Id of the member.",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="string")
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
 *           ref="#/components/schemas/volunteer_hour_summary_list"
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
use App\Error\PermissionDeniedException;
use App\Error\NotFoundException;
use Atlas\Query\Select;
use App\Controller\BaseController;
use \App\Controller\IncludeResource;

class GetMemberHoursSummary extends BaseHours
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

        $sum = $this->staffHours($request, $response, $id, $params);
        if ($sum !== null) {
            return [
            \App\Controller\BaseController::LIST_TYPE,
            [],
            array('type' => 'volunteer_hour_summary_list', 'total_hours' => $sum, 'total_volunteer_count' => 1)];
        }

        $data = Select::new($this->container->db)
        ->columns('"volunteer_hour_summary" AS type')
        ->columns('DepartmentID AS department')
        ->columns('AccountID AS member')
        ->columns('COUNT(HourEntryID) AS entry_count')
        ->columns('SUM(ActualHours * TimeModifier) AS total_hours')
        ->from('VolunteerHours')
        ->whereEquals(['AccountID' => $id, 'EventID' => $event])
        ->groupBy('DepartmentID')
        ->fetchAll();

        if (empty($data)) {
            throw new NotFoundException('Volunteer records not found');
        }

        $sum = 0;
        foreach ($data as $entry) {
            $sum += $entry['total_hours'];
        }

        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        ['type' => 'volunteer_hour_summary_list', 'total_hours' => $sum, 'total_volunteer_count' => 1]];

    }


    /* end GetMemberHoursSummary */
}
