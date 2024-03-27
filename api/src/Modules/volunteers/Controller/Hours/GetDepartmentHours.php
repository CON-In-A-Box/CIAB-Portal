<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"volunteers"},
 *      path="/department/{id}/volunteer/hours",
 *      summary="Gets department volunteer data",
 *      @OA\Parameter(
 *          description="Id or name of the department",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(
 *               description="Department id or name",
 *               type="string"
 *          )
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
use App\Error\PermissionDeniedException;
use App\Error\NotFoundException;
use Atlas\Query\Select;
use App\Controller\BaseController;
use \App\Controller\IncludeResource;

class GetDepartmentHours extends BaseHours
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.get.volunteer.hours'];
        $this->checkPermissions($permissions);
        $event = $this->getEventId($request);
        $id = $this->getDepartment($params['id'])['id'];

        $data = Select::new($this->container->db)
        ->columns(...BaseHours::selectMapping())
        ->from('VolunteerHours')
        ->whereEquals(['DepartmentID' => $id, 'EventID' => $event])
        ->fetchAll();

        if (empty($data)) {
            throw new NotFoundException('Volunteer records not found');
        }

        $sum = 0;
        foreach ($data as &$entry) {
            $sum += $entry['hours'] * $entry['modifier'];

            /* some data conversion */
            $datetime = \DateTime::createFromFormat('Y-m-d H:i:s', $entry['end']);
            $entry['end'] = $datetime->format(\DateTime::RFC3339);
        }

        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        array('type' => 'volunteer_hour_entry_list', 'total_hours' => $sum)];

    }


    /* end GetDepartmentHours */
}
