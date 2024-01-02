<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Get(
 *      tags={"members"},
 *      path="/member/{id}/status",
 *      summary="Gets the status of an member account.",
 *      @OA\Parameter(
 *          description="login name or member id for the account",
 *          in="path",
 *          name="id",
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

use App\Error\NotFoundException;

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


    public function buildResource(Request $request, Response $response, $params): array
    {
        $data = $this->getMember($request, $params['name']);
        if (count($data) > 1) {
            $error = 'Too many matches found';
            throw new NotFoundException($error);
        }
        $data = $data[0];
        if (!array_key_exists('id', $data)) {
            $error = 'User ID not found';
            throw new NotFoundException($error);
        }
        $valid = array('type' => 'member_status',
                       'status' => $this->verifyAccount($data['id']));
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $valid];

    }


    /* end GetStatus */
}
