<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Member;

use Slim\Http\Request;
use Slim\Http\Response;

use App\Controller\PermissionDeniedException;
use App\Controller\ConflictException;

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
        $sql = "SELECT * FROM  `Authentication` WHERE AccountID = $user;";
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
        if (!$this->privilaged) {
            if ($accountID === null || !\ciab\RBAC::havePermission("api.put.member.password")) {
                $attribute = $request->getAttribute('oauth2-token');
                if ($attribute) {
                    $user = $attribute['user_id'];
                } else {
                    if (array_key_exists('OneTimeCode', $body)) {
                        $onetime = $body['OneTimeCode'];
                        $now = date('Y-m-d H:i', strtotime("now"));
                        $sql = "SELECT AccountID FROM Authentication WHERE "."OneTime = '$onetime' AND OneTimeExpires > "."'$now'";
                        $sth = $this->container->db->prepare($sql);
                        $sth->execute();
                        $result = $sth->fetchAll();
                        if (!empty($result) &&
                            $accountID = $result[0]['AccountID']) {
                            return null;
                        }
                    }
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
        return null;

    }


    public function buildResource(Request $request, Response $response, $args): array
    {
        $accountID = $args['id'];
        $body = $request->getParsedBody();
        $response = $this->verifyAccess($request, $response, $body, $accountID);
        if ($response) {
            return $response;
        }

        $password = $body['NewPassword'];
        $this->setPassword($accountID, $password);
        return [null];

    }


    /* end PutPassword */
}
