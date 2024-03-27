<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"volunteers"},
 *      path="/event/{id}/volunteer/claims/summary",
 *      summary="Gets event volunteer summary",
 *      @OA\Parameter(
 *          description="Id of the event.",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          ref="#/components/parameters/event"
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Event volunteer data found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/volunteer_claim_summary"
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

namespace App\Modules\volunteers\Controller\Claims;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Error\NotFoundException;
use Atlas\Query\Select;
use App\Controller\BaseController;
use \App\Controller\IncludeResource;

class GetEventClaimsSummary extends BaseClaims
{


    public function __construct($container)
    {
        parent::__construct($container);
        $this->includes = [
        new IncludeResource('\App\Controller\Event\GetEvent', 'id', 'event'),
        ];

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $permissions = ['api.get.volunteer.hours'];
        $this->checkPermissions($permissions);
        $id = $this->getEvent($params['id'])['id'];

        $data = Select::new($this->container->db)
        ->columns('Value')
        ->columns('Promo')
        ->columns('EventID as event')
        ->from('HourRedemptions AS v')
        ->whereEquals(['EventID' => $id])
        ->join(
            'LEFT',
            'VolunteerRewards AS d',
            'v.PrizeID = d.PrizeID'
        )
        ->fetchAll();

        if (empty($data)) {
            throw new NotFoundException('Volunteer records not found');
        }

        $sum = 0.0;
        foreach ($data as $entry) {
            if ($entry['Promo']) {
                continue;
            }
            $sum += $entry['Value'];
        }

        $output = [
            'type' => 'volunteer_claim_summary',
            'reward_count' => count($data),
            'spent_hours' => $sum,
            'event' => $data[0]['event']
        ];

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $output];

    }


    /* end GetEventClaimsSummary */
}
