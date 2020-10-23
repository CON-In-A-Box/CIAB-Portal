<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Member;

use Slim\Http\Request;
use Slim\Http\Response;

class PostMember extends BaseMember
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $body = $request->getParsedBody();
        if (array_key_exists('email', $body)) {
            $body['email1'] = $body['email'];
        }
        if (array_key_exists('legalFirstName', $body)) {
            $body['firstName'] = $body['legalFirstName'];
        }
        if (array_key_exists('legalFirstLast', $body)) {
            $body['lastName'] = $body['legalLastName'];
        }
        if (!$body || !array_key_exists('email1', $body)) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, "Required 'email1' parameter not present", 'Missing Parameter', 400)
            ];
        }
        $user = \lookup_users_by_email($body['email1']);
        if (count($user['users']) > 0) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, "Account with Email Already Exists", 'Conflict', 409)
            ];
        }
        if (!array_key_exists('firstName', $body) &&
            !array_key_exists('lastName', $body)) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, "Required 'firstName' and/or 'lastName' parameter not present", 'Missing Parameter', 400)
            ];
        }

        if (array_key_exists('Neon', $GLOBALS)) {
            $accountID = \neon_createUser(
                $body['email1'],
                $body['firstName'],
                $body['lastName']
            );
        } else {
            $accountID = \createUser($body['email1'], 1000);
        }
        if (!$accountID) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, "Account Creation Failed", 'Conflict', 409)
            ];
        }

        $target = new \App\Controller\Member\PutMember($this->container);
        $target->privilaged = true;
        $data = $target->buildResource($request, $response, ['name' => $accountID])[1];
        $result = $target->arrayResponse($request, $response, $data);

        $pwd = new \App\Controller\Member\PostPassword($this->container);
        $pwd->privilaged = true;
        $pwd->buildResource($request, $response, ['name' => $accountID]);

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $result
        ];

    }


    /* end PostMember */
}
