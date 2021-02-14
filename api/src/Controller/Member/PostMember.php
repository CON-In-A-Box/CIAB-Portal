<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Member;

use Slim\Http\Request;
use Slim\Http\Response;

use App\Controller\InvalidParameterException;
use App\Controller\ConflictException;

class PostMember extends BaseMember
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $body = $request->getParsedBody();
        if ($body && array_key_exists('email', $body)) {
            $body['email1'] = $body['email'];
        }
        if (!$body || !array_key_exists('email1', $body)) {
            throw new InvalidParameterException("Required 'email1' parameter not present");
        }
        $user = \lookup_users_by_email($body['email1']);
        if (count($user['users']) > 0) {
            throw new ConflictException("Account with Email Already Exists");
        }
        if (array_key_exists('legalFirstName', $body)) {
            $body['firstName'] = $body['legalFirstName'];
        }
        if (array_key_exists('legalFirstLast', $body)) {
            $body['lastName'] = $body['legalLastName'];
        }
        if (!array_key_exists('firstName', $body) &&
            !array_key_exists('lastName', $body)) {
            throw new InvalidParameterException("Required 'firstName' and/or 'lastName' parameter not present");
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
            throw new ConflictException("Account Creation Failed");
        }

        $target = new \App\Controller\Member\PutMember($this->container);
        $target->privilaged = true;
        $data = $target->buildResource($request, $response, ['id' => $accountID])[1];
        $result = $target->arrayResponse($request, $response, $data);

        $pwd = new \App\Controller\Member\PostPassword($this->container);
        $pwd->privilaged = true;
        $pwd->buildResource($request, $response, ['email' => $accountID]);

        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $result
        ];

    }


    /* end PostMember */
}
