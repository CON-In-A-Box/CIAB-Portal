<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"members"},
 *      path="/member/{name}/status",
 *      summary="Gets the status of an member account.",
 *      @OA\Parameter(
 *          description="login for the account",
 *          in="path",
 *          name="name",
 *          required=true,
 *          @OA\Schema(type="string")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Member status found",
 *          @OA\JsonContent(
 *              @OA\Property(
 *                  property="type",
 *                  type="string",
 *                  enum={"member_status"}
 *              ),
 *              @OA\Property(
 *                  property="status",
 *                  type="integer",
 *                  description="Member account status code",
 *                  enum={0,1,2,3}
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/member_not_found"
 *      )
 *  )
 **/

namespace App\Controller\Member;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;

use App\Controller\NotFoundException;

require_once(__DIR__.'/../../../../functions/authentication.inc');

class GetStatus extends BaseMember
{


    private function verifyAccount($account)
    {
        global $MAXLOGINFAIL;

        $max_fail = 5;
        if (isset($MAXLOGINFAIL) && !empty($MAXLOGINFAIL)) {
            $max_fail = intval($MAXLOGINFAIL);
        }

        $select = Select::new($this->container->db);
        $select->columns('FailedAttempts', 'Expires')->from('Authentication')->whereEquals(['AccountID' => $account]);
        $value = $select->fetchOne();
        if ($value !== false) {
            if ($value['FailedAttempts'] >= $max_fail) {
                return AUTH_LOCKED;
            }
            $now = strtotime("now");
            $expire = strtotime($value['Expires']);
            if ($now > $expire) {
                return AUTH_EXPIRED;
            }
            return AUTH_SUCCESS;
        } else {
            return AUTH_BAD;
        }

    }


    public function buildResource(Request $request, Response $response, $args): array
    {
        $data = \lookup_users_by_key($args['name']);
        if (empty($data['users'])) {
            if (empty($data['error'])) {
                $error = 'No Members Found';
            } else {
                $error = $data['error'];
            }
            throw new NotFoundException($error);
        }
        if (count($data['users']) > 1) {
            $error = 'Too many matches found';
            throw new NotFoundException($error);
        }
        $data = $data['users'][0];
        if (!array_key_exists('Id', $data)) {
            $error = 'User ID not found';
            throw new NotFoundException($error);
        }
        $valid = array('type' => 'member_status',
                       'status' => $this->verifyAccount($data['Id']));
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $valid];

    }


    /* end GetStatus */
}
