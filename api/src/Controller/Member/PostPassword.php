<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Member;

require_once __DIR__.'/../../../../backends/email.inc';

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views;

class PostPassword extends BaseMember
{


    private static function getId($valLength = 40)
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

        $user = $data['id'];
        $email = $data['email'];
        $code = PostPassword::getId();
        $oneexpired = date('Y-m-d H:i', strtotime($duration));
        $newauth = $code;
        $auth = 'NULL';
        $last = 'NULL';
        $realexpires = 'NULL';
        $exists = false;

        $sql = "SELECT * FROM  `Authentication` WHERE AccountID = $user;";
        $result = $this->container->db->prepare($sql);
        $result->execute();
        $value = $result->fetch();
        if ($value !== false) {
            if (!empty($value['Authentication'])) {
                $auth = "'".$value['Authentication']."'";
            }
            if (!empty($value['LastLogin'])) {
                $last = "'".$value['LastLogin']."'";
            }
            if (!empty($value['Expires'])) {
                $realexpires = "'".$value['Expires']."'";
            }
            $exists = true;
        }

        $sql = <<<SQL
            REPLACE INTO `Authentication`
            SET AccountID = $user,
                OneTime = '$newauth',
                OneTimeExpires= '$oneexpired',
                Authentication = $auth,
                LastLogin = $last,
                Expires = $realexpires,
                FailedAttempts = 0;
SQL;
        $this->container->db->prepare($sql)->execute();

        $phpView = new Views\PhpRenderer(__DIR__.'/../../Templates', [
            'name' => $data['firstName'],
            'con' => $CONSITENAME,
            'code' => urlencode($code),
            'url' => $BASEURL.'?Function=recovery',
            'expire' => $oneexpired,
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


    public function buildResource(Request $request, Response $response, $args): array
    {
        $data = $this->findMember($request, $response, $args, 'email');
        $this->resetPassword($response, $data);
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        [null],
        201
        ];

    }


    /* end PostPassword */
}
