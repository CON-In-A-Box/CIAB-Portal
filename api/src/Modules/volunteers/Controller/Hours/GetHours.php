<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"volunteers"},
 *      path="/volunteers/hours/{id}",
 *      summary="Gets volunteer data",
 *      @OA\Parameter(
 *          description="Id of the volunteer record.",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="volunteer data found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/volunteer_hour_entry"
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/volunteer_entry_not_found"
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

class GetHours extends BaseHours
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $user = $request->getAttribute('oauth2-token')['user_id'];

        $data = Select::new($this->container->db)
        ->columns(...BaseHours::selectMapping())
        ->from('VolunteerHours')
        ->whereEquals(['HourEntryID' => $params['id']])
        ->fetchOne();

        if (empty($data)) {
            throw new NotFoundException('Volunteer records not found');
        }

        if ($data['member'] !== $user) {
            $permissions = ['api.get.volunteer.hours'];
            $this->checkPermissions($permissions);
        }

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $data];

    }


    /* end GetHours */
}
