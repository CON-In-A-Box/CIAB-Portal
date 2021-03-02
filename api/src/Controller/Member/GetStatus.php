<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Member;

use Slim\Http\Request;
use Slim\Http\Response;

use App\Controller\NotFoundException;

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

        $sql = <<<SQL
            SELECT * FROM `Authentication` WHERE AccountID = $account;
SQL;
        $result = $this->container->db->prepare($sql);
        $result->execute();
        $value = $result->fetch();
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


    public function buildResource(Request $request, Response $response, $args): array
    {
        $data = \lookup_users_by_key($args['name']);
        if (empty($data['users'])) {
            if (empty($data['error'])) {
                $error = 'No Members Found';
            } else {
                $error = $data['error'];
            }
            throw new NotFoundException($error);
        }
        if (count($data['users']) > 1) {
            $error = 'Too many matches found';
            throw new NotFoundException($error);
        }
        $data = $data['users'][0];
        if (!array_key_exists('Id', $data)) {
            $error = 'User ID not found';
            throw new NotFoundException($error);
        }
        $valid = array('type' => 'member_status',
                       'status' => $this->verifyAccount($data['Id']));
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $valid];

    }


    /* end GetStatus */
}
