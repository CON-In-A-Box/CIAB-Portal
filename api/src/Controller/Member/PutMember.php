<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Member;

use Slim\Http\Request;
use Slim\Http\Response;

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


    public function buildResource(Request $request, Response $response, $args): array
    {
        $data = $this->findMemberId($request, $response, $args, 'id');
        $accountID = $data['id'];

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

        if (array_key_exists('email', $body)) {
            $body['email1'] = $body['email'];
        }
        if (array_key_exists('legalFirstName', $body)) {
            $body['firstName'] = $body['legalFirstName'];
        }
        if (array_key_exists('legalLastName', $body)) {
            $body['lastName'] = $body['legalLastName'];
        }
        PutMember::checkBoolParam($body, 'Deceased');
        PutMember::checkBoolParam($body, 'DoNotContact');
        PutMember::checkBoolParam($body, 'EmailOptOut');
        PutMember::checkDateParam($body, 'Birthdate');

        \updateAccount($body, $accountID);

        $target = new \App\Controller\Member\GetMember($this->container);
        $data = $target->buildResource($request, $response, $args)[1];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data)
        ];

    }


    /* end PutMember */
}
