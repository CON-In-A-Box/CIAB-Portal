<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"volunteers"},
 *      path="/member/{id}/volunteer/claims/summary",
 *      summary="Gets member volunteer summary",
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
 *      @OA\Response(
 *          response=200,
 *          description="Member volunteer data found",
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
use App\Controller\NotFoundException;
use Atlas\Query\Select;
use App\Controller\BaseController;
use \App\Controller\IncludeResource;

class GetMemberClaimsSummary extends BaseClaims
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
        $user = $request->getAttribute('oauth2-token')['user_id'];
        $event = $this->getEventId($request);
        if (array_key_exists('id', $params)) {
            $id = $this->getMember($request, $params['id'], null, false)[0]['id'];
        }

        if ($id == null) {
            $id = $user;
        }

        if ($id !== $user) {
            $permissions = ['api.get.volunteer.claims'];
            $this->checkPermissions($permissions);
        }

        $data = Select::new($this->container->db)
        ->columns('Value')
        ->columns('Promo')
        ->columns('EventID as event')
        ->from('HourRedemptions AS v')
        ->whereEquals(['AccountID' => $id, 'EventID' => $event])
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
            'type' => 'volunteer_user_summary',
            'reward_count' => count($data),
            'spent_hours' => $sum,
            'event' => $data[0]['event']
        ];

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $output];

    }


    /* end GetMemberClaimsSummary */
}
