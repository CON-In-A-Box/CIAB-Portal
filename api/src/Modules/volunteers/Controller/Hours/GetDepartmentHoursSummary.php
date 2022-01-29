<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"volunteers"},
 *      path="/department/{id}/volunteer/hours/summary",
 *      summary="Gets department volunteer summary",
 *      @OA\Parameter(
 *          description="Id or name of the department",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(
 *              oneOf = {
 *                  @OA\Schema(
 *                      description="Department id",
 *                      type="integer"
 *                  ),
 *                  @OA\Schema(
 *                      description="Department name",
 *                      type="string"
 *                  )
 *              }
 *          )
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/event",
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/short_response",
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

class GetDepartmentHoursSummary extends BaseHours
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.get.volunteer.hours'];
        $this->checkPermissions($permissions);
        $event = $this->getEventId($request);
        $id = $this->getDepartment($params['id'])['id'];

        $data = Select::new($this->container->db)
        ->columns('"volunteer_hour_summary" AS type')
        ->columns('AccountID AS member')
        ->columns('COUNT(HourEntryID) AS entry_count')
        ->columns('SUM(ActualHours * TimeModifier) AS total_hours')
        ->from('VolunteerHours')
        ->whereEquals(['DepartmentID' => $id, 'EventID' => $event])
        ->groupBy('AccountID')
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
        array('type' => 'volunteer_hour_entry_list', 'total_hours' => $sum)];

    }


    /* end GetDepartmentHoursSummary */
}
