<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Put(
 *      tags={"members"},
 *      path="/member/{id}",
 *      summary="Updates a member",
 *      @OA\Parameter(
 *          description="The id or login of the member",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(
 *              oneOf = {
 *                  @OA\Schema(
 *                      description="Member login",
 *                      type="string"
 *                  ),
 *                  @OA\Schema(
 *                      description="Member id",
 *                      type="integer"
 *                  )
 *              }
 *          )
 *      ),
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  ref="#/components/schemas/member_body"
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="OK",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/member"
 *          )
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Parameter is missing or invalid",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/error"
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          ref="#/components/responses/401"
 *      ),
 *      security={{"ciab_auth":{}}}
 *  )
 **/

namespace App\Controller\Member;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Update;
use App\Controller\PermissionDeniedException;
use App\Controller\NotFoundException;
use App\Controller\InvalidParameterException;

class PutMember extends BaseMember
{

    public $privilaged = false;


    protected static function checkBoolParam(&$body, $param)
    {
        if (array_key_exists($param, $body)) {
            $value = filter_var($body[$param], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($value === null) {
                throw new InvalidParameterException("'$param' parameter not valid boolean.");
            }
            $body[$param] = (int) $value;
        }

    }


    protected static function checkDateParam(&$body, $param)
    {
        if (array_key_exists($param, $body)) {
            $date = strtotime($body[$param]);
            if (!$date) {
                throw new InvalidParameterException("'$param' parameter not valid date.");
            }
            $value = date("Y-m-d", $date);
            $body[$param] = "$value";
        }

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $accountID = $this->getMember($request, $params['id'])[0]['id'];

        if (!$this->privilaged) {
            $user = $request->getAttribute('oauth2-token')['user_id'];
            if ($accountID != $user &&
                !\ciab\RBAC::havePermission("api.put.member")) {
                throw new PermissionDeniedException();
            }
        }

        $body = $request->getParsedBody();
        if (empty($body)) {
            throw new NotFoundException('No update parameter present');
        }

        PutMember::checkBoolParam($body, 'deceased');
        PutMember::checkBoolParam($body, 'do_not_contact');
        PutMember::checkBoolParam($body, 'email_optout');
        PutMember::checkDateParam($body, 'birthdate');

        Update::new($this->container->db)
            ->table('Members')
            ->columns(BaseMember::insertPayloadFromParams($body, false))
            ->whereEquals(['AccountID' => $params['id']])
            ->perform();

        $data = $this->getMember($request, $params['id'], 'id');
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $data[0]
        ];

    }


    /* end PutMember */
}
