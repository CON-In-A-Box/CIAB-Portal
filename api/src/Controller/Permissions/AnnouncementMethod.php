<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Permissions;

use Slim\Http\Request;
use Slim\Http\Response;

use App\Controller\NotFoundException;

class AnnouncementMethod extends AnnouncementPermission
{


    public function buildResource(Request $request, Response $response, $args): array
    {
        global $Departments;


        $methodArg = null;
        if (array_key_exists('method', $args)) {
            $methodArg = $args['method'];
        }
        if ($methodArg !== null) {
            if (!in_array($methodArg, AnnouncementPermission::ALL_METHODS)) {
                throw new NotFoundException('Method \'announcement.'.$methodArg.'\' Invalid');
            }
        }
        $path = $request->getUri()->getBaseUrl();
        if (array_key_exists('department', $args)) {
            $data = $this->getDepartment($args['department']);
            if ($data === null) {
                throw new NotFoundException('Department \''.$args['department'].'\' Invalid');
            }
            $allowed = (\ciab\RBAC::havePermission('api.'.$methodArg.'.announcement.'.$data['id']) ||
                        \ciab\RBAC::havePermission('api.'.$methodArg.'.announcement.all'));
            ;
            $result = $this->buildDeptEntry(
                $data['id'],
                $allowed,
                'announcement',
                $methodArg,
                [
                'method' => $methodArg,
                'href' => $path.'/announcement/'.$data['id'],
                'request' => strtoupper($methodArg)
                ]
            );
            return [
            \App\Controller\BaseController::RESOURCE_TYPE,
            $result];
        } else {
            $output = array();
            if ($methodArg !== null) {
                $methods = [$methodArg];
            } else {
                $methods = AnnouncementPermission::ALL_METHODS;
            }
            foreach ($methods as $method) {
                foreach ($Departments as $key => $data) {
                    $allowed = (\ciab\RBAC::havePermission('api.'.$method.'.announcement.'.$data['id']) ||
                                \ciab\RBAC::havePermission('api.'.$method.'.announcement.all'));
                    ;
                    if ($allowed) {
                        $result = $this->buildDeptEntry(
                            $data['id'],
                            $allowed,
                            'announcement',
                            $method,
                            [
                            'method' => $method,
                            'href' => $path.'/announcement/'.$data['id'],
                            'request' => strtoupper($method)
                            ]
                        );
                        $output[] = $result;
                    }
                }
            }
            return [
            \App\Controller\BaseController::LIST_TYPE,
            $output,
            array('type' => 'permission_list')];
        }

    }


    /* end AnnouncementMethod */
}
