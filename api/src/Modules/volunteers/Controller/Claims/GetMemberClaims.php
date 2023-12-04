<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"volunteers"},
 *      path="/member/{id}/volunteers/claims",
 *      summary="Gets member volunteer claims",
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
 *          description="volunteer claim data found",
 *          @OA\JsonContent(
 *           ref="#/components/schemas/volunteer_claim_list"
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
use App\Error\NotFoundException;
use Atlas\Query\Select;
use App\Controller\BaseController;
use \App\Controller\IncludeResource;

class GetMemberClaims extends BaseClaims
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
        ->columns(...BaseClaims::selectMapping())
        ->from('HourRedemptions')
        ->whereEquals(['AccountID' => $id, 'EventID' => $event])
        ->fetchAll();

        if (empty($data)) {
            throw new NotFoundException('Volunteer records not found');
        }

        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        array('type' => 'volunteer_claim_list')];

    }


    /* end GetMemberClaims */
}
