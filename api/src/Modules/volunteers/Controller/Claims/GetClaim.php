<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"volunteers"},
 *      path="/volunteers/claims/{id}",
 *      summary="Gets volunteer claim",
 *      @OA\Parameter(
 *          description="Id of the volunteer claim.",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="volunteer claim data found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/volunteer_claim"
 *          ),
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/volunteer_claim_not_found"
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

class GetClaim extends BaseClaims
{


    public function __construct($container)
    {
        parent::__construct($container);
        $this->includes = [
        new IncludeResource('\App\Controller\Member\GetMember', 'id', 'member'),
        new IncludeResource('\App\Controller\Event\GetEvent', 'id', 'event'),
        new IncludeResource('\App\Modules\volunteers\Controller\Rewards\GetReward', 'id', 'reward')
        ];

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $user = $request->getAttribute('oauth2-token')['user_id'];

        $data = Select::new($this->container->db)
        ->columns(...BaseClaims::selectMapping())
        ->from('HourRedemptions')
        ->whereEquals(['ClaimID' => $params['id']])
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


    /* end GetClaim */
}
