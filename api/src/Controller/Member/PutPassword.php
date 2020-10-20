<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Member;

use Slim\Http\Request;
use Slim\Http\Response;

class PutPassword extends BaseMember
{

    public $privilaged = false;


    public function buildResource(Request $request, Response $response, $args): array
    {
        $data = $this->findMember($request, $response, $args, 'name');
        if (gettype($data) === 'object') {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $data];
        }
        $accountID = $data['id'];

        $body = $request->getParsedBody();
        $password = $body['NewPassword'];

        if (!$this->privilaged) {
            if (!\ciab\RBAC::havePermission("api.put.member.password")) {
                $attribute = $request->getAttribute('oauth2-token');
                if ($attribute) {
                    $user = $attribute['user_id'];
                } else {
                    $user = -1;
                }
                if ($accountID != $user) {
                    return [
                    \App\Controller\BaseController::RESULT_TYPE,
                    $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
                }
                if (!array_key_exists('OldPassword', $body) ||
                    \check_authentication(
                        $accountID,
                        $body['OldPassword'],
                        false
                    ) != 0) {
                    return [
                    \App\Controller\BaseController::RESULT_TYPE,
                    $this->errorResponse($request, $response, 'Invalid Existing Password', 'Permission Denied', 403)];
                }
            }
        }

        if (array_key_exists('Temporary', $body) &&
            boolval($body['Temporary'])) {
            \reset_password($data['email'], $password);
        } else {
            \set_password($accountID, $password);
        }
        return [null];

    }


    /* end PutPassword */
}
