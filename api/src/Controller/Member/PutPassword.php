<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Put(
 *      tags={"members"},
 *      path="/member/{id}/password",
 *      summary="Updates a member's password",
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
 *                  @OA\Property(
 *                      property="OldPassword",
 *                      type="string",
 *                      nullable=false
 *                  ),
 *                  @OA\Property(
 *                      property="NewPassword",
 *                      type="string",
 *                      nullable=false
 *                  )
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
 *
 *  @OA\Put(
 *      tags={"members"},
 *      path="/member/{email}/password/recovery",
 *      summary="Updates a member's password with recovery code",
 *      @OA\Parameter(
 *          description="The login email of the member",
 *          in="path",
 *          name="email",
 *          required=true,
 *          @OA\Schema(type="string"),
 *      ),
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="OneTimeCode",
 *                      type="string",
 *                      nullable=false
 *                  ),
 *                  @OA\Property(
 *                      property="NewPassword",
 *                      type="string",
 *                      nullable=false
 *                  )
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="OK"
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Parameter is missing or invalid",
 *          @OA\JsonContent(
 *              ref="#/components/schemas/error"
 *          )
 *      )
 *  )
 **/

namespace App\Controller\Member;

use Slim\Http\Request;
use Slim\Http\Response;

use App\Controller\PermissionDeniedException;
use App\Controller\ConflictException;
use App\Controller\InvalidParameterException;

class PutPassword extends BaseMember
{

    public $privilaged = false;


    private function setPassword($user, $password)
    {
        global $PASSWORDEXPIRE;

        if (isset($PASSWORDEXPIRE) && !empty($PASSWORDEXPIRE)) {
            $duration = $PASSWORDEXPIRE;
        } else {
            $duration = '+1 year';
        }
        $expires = date('Y-m-d H:i', strtotime($duration));
        $auth = \password_hash($password, PASSWORD_DEFAULT);

        $last = 'NULL';
        $sql = "SELECT * FROM  `Authentication` WHERE AccountID = '$user';";
        $result = $this->container->db->prepare($sql);
        $result->execute();
        $value = $result->fetch();
        if ($value !== false && $value['LastLogin'] !== null) {
            $last = "'".$value['LastLogin']."'";
        }

        $sql = <<<SQL
            REPLACE INTO `Authentication`
            SET AccountID = $user,
                Authentication = '$auth',
                LastLogin = $last,
                Expires = '$expires',
                FailedAttempts = 0,
                OneTime = NULL,
                OneTimeExpires = NULL;
SQL;
        $result = $this->container->db->prepare($sql);
        $result->execute();

    }


    private function verifyAccess($request, $response, $body, &$accountID)
    {
        if (array_key_exists('OneTimeCode', $body)) {
            $onetime = $body['OneTimeCode'];
            $now = date('Y-m-d H:i', strtotime("now"));
            $sql = "SELECT AccountID FROM Authentication WHERE OneTime = '$onetime' AND OneTimeExpires > '$now'";
            $sth = $this->container->db->prepare($sql);
            $sth->execute();
            $result = $sth->fetchAll();
            if (empty($result) || $accountID != $result[0]['AccountID']) {
                throw new PermissionDeniedException();
            }
            return;
        }

        if (!$this->privilaged) {
            if (!\ciab\RBAC::havePermission("api.put.member.password")) {
                $attribute = $request->getAttribute('oauth2-token');
                if ($attribute) {
                    $user = $attribute['user_id'];
                } else {
                    $user = -1;
                }
                if ($accountID != $user) {
                    throw new PermissionDeniedException();
                }
                if (!array_key_exists('OldPassword', $body) ||
                    \check_authentication(
                        $accountID,
                        $body['OldPassword'],
                        false
                    ) != 0) {
                    throw new ConflictException('Invalid Existing Password');
                }
            }
        }

    }


    public function buildResource(Request $request, Response $response, $args): array
    {
        if (array_key_exists('email', $args)) {
            $index = 'email';
        } else {
            $index = 'id';
        }
        $data = $this->findMember($request, $response, $args, $index);
        $accountID = $data['id'];
        $body = $request->getParsedBody();
        if (empty($body)) {
            throw new InvalidParameterException('No body present');
        }
        $this->verifyAccess($request, $response, $body, $accountID);

        if ($body) {
            if (!array_key_exists('NewPassword', $body)) {
                throw new InvalidParameterException("'NewPassword' not supplied");
            }
            $password = $body['NewPassword'];
            $this->setPassword($accountID, $password);
        }
        return [null];

    }


    /* end PutPassword */
}
