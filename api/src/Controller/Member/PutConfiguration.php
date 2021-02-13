<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Member;

use Slim\Http\Request;
use Slim\Http\Response;

use App\Controller\PermissionDeniedException;

class PutConfiguration extends BaseMember
{

    use \App\Controller\TraitConfiguration;


    public function buildResource(Request $request, Response $response, $args): array
    {
        $data = $this->findMemberId($request, $response, $args, 'id');
        if (gettype($data) === 'object') {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $data];
        }
        $accountID = $data['id'];

        $user = $request->getAttribute('oauth2-token')['user_id'];
        if ($accountID != $user &&
            !\ciab\RBAC::havePermission("api.put.member")) {
            throw new PermissionDeniedException();
        }

        $body = $request->getParsedBody();
        $body['AccountID'] = $accountID;
        return $this->putConfiguration($request, $response, $args, 'AccountConfiguration', $body);

    }


    /* end PutConfiguration */
}
