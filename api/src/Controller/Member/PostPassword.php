<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

/**
 *  @OA\Post(
 *      tags={"members"},
 *      path="/member/{id}/password",
 *      summary="Requests a password reset for a member",
 *      @OA\Parameter(
 *          description="The id or login email for the member",
 *          in="path",
 *          name="id",
 *          required=true,
 *          @OA\Schema(
 *              description="Member ID or login email",
 *              type="string"
 *          )
 *      ),
 *      @OA\Response(
 *          response=201,
 *          description="OK"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          ref="#/components/responses/member_not_found"
 *      )
 *  )
 **/

namespace App\Controller\Member;

require_once __DIR__.'/../../../../backends/email.inc';

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views;
use Atlas\Query\Select;
use Atlas\Query\Insert;
use Atlas\Query\Update;

class PostPassword extends BaseMember
{

    protected static $columnsToAttributes = [
    'AccountID' => 'AccountID',
    'OneTime' => 'OneTime',
    'OneTimeExpires' => 'OneTimeExpires',
    'Authentication' => 'Authentication',
    'LastLogin' => 'LastLogin',
    'Expires' => 'Expires',
    'FailedAttempts' => 'FailedAttempts'
    ];


    private static function getId($valLength = 40): string
    {
        $result = '';
        $moduleLength = 40;
        $steps = round(($valLength / $moduleLength) + 0.5);

        for ($i = 0; $i < $steps; $i++) {
            $result .= \sha1(uniqid().\md5(\rand().\uniqid()));
        }

        return substr($result, 0, $valLength);

    }


    private function resetPassword(Response $response, $data)
    {
        global $CONSITENAME, $PASSWORDRESET, $BASEURL;

        if (isset($PASSWORDRESET) && !empty($PASSWORDRESET)) {
            $duration = $PASSWORDRESET;
        } else {
            $duration = '+60 minutes';
        }

        $select = Select::new($this->container->db);
        $select->columns('*')->from('Authentication')->whereEquals(['AccountID' => $data['id']]);
        $values = $select->fetchOne();
        if ($values !== null) {
            $modify = Update::new($this->container->db);
            $modify->table('Authentication');
            $modify->whereEquals(['AccountID' => $data['id']]);
            $exists = true;
        } else {
            $modify = Insert::new($this->container->db);
            $modify->into('Authentication');
            $values['Authentication'] = null;
            $values['Expires'] = null;
            $values['LastLogin'] = null;
            $exists = false;
        }
        $email = $data['email'];
        $values['AccountID'] = $data['id'];
        $code = PostPassword::getId();
        $values['OneTime'] = $code;
        $values['OneTimeExpires'] = date('Y-m-d H:i', strtotime($duration));
        $values['FailedAttempts'] = 0;
        $modify->columns(PostPassword::insertPayloadFromParams($values, false));
        $modify->perform();

        $name = 'Member';
        if (array_key_exists('first_name', $data)) {
            $name = $data['first_name'];
        } elseif (array_key_exists('last_name', $data)) {
            $name = $data['last_name'];
        }

        $phpView = new Views\PhpRenderer(__DIR__.'/../../Templates', [
            'name' => $name,
            'con' => $CONSITENAME,
            'code' => urlencode($code),
            'url' => $BASEURL.'?Function=recovery',
            'expire' => $values['OneTimeExpires'],
            'email' => urlencode($email)
        ]);
        if ($exists) {
            $subject = 'Password Reset Request';
            $adminMessage = "A password reset has been requested for '$email' on the '$CONSITENAME' web site.\n";
            $adminMessage .= "The new authorization code is '$code' \n";
            $phpView->render($response, 'passwordReset.phtml');
        } else {
            $subject = "$CONSITENAME Create Password";
            $adminMessage = "Password creation has been requested for '$email' on the '$CONSITENAME' web site.\n";
            $adminMessage .= "The authorization code is '$code' \n";
            $phpView->render($response, 'newPassword.phtml');
        }
        $response->getBody()->rewind();
        $message = $response->getBody()->getContents();
        \ciab\Email::mail($email, \getNoReplyAddress(), $subject, $message, null, 'text/html');
        \ciab\Email::mail(\getSecurityEmail(), \getNoReplyAddress(), $subject, $adminMessage);
        error_log($adminMessage);

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        $data = $this->getMember($request, $params['email']);
        $this->resetPassword($response, $data[0]);
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        [null],
        201
        ];

    }


    /* end PostPassword */
}
