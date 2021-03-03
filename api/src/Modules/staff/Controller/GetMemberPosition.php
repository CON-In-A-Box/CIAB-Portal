<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\staff\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Controller\PermissionDeniedException;
use App\Controller\NotFoundException;

class GetMemberPosition extends BaseStaff
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        $user = $request->getAttribute('oauth2-token')['user_id'];
        if (array_key_exists('id', $args)) {
            $data = \lookup_users_by_key($args['id']);
            if (empty($data['users'])) {
                if (empty($data['error'])) {
                    $error = 'No Members Found';
                } else {
                    $error = $data['error'];
                }
                throw new NotFoundException($error);
            }
            if ($data['users'][0]['Id'] != $user &&
                !\ciab\RBAC::havePermission('api.get.staff')) {
                throw new PermissionDeniedException();
            }
            $user = $data['users'][0]['Id'];
        }
        $staff = $this->getStaffPosition($user);
        $data = [];
        $path = $request->getUri()->getBaseUrl();
        foreach ($staff as $entry) {
            $data[] = $this->buildEntry($request, $entry['ListRecordID'], $entry['DepartmentID'], $user, $entry['Note'], $entry['Position']);
        }
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        array('type' => 'staff_list')
        ];

    }


    /* end GetMemberPosition */
}
