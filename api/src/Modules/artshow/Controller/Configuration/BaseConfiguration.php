<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Configuration;

use Slim\Container;
use App\Modules\artshow\Controller\BaseArtshow;

abstract class BaseConfiguration extends BaseArtshow
{


    public function __construct(Container $container)
    {
        parent::__construct("configuration", $container);

    }


    protected function deleteConfigValue($type, $table, $field)
    {
        $output = array();
        $sth = $this->container->db->prepare("DELETE FROM `$table` WHERE `$field` = '$type'");
        $sth->execute();
        return [null];

    }


    protected function checkPutPermission($request, $response)
    {
        if (!\ciab\RBAC::havePermission('api.set.artshow.configuration')) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }
        return null;

    }


    protected function checkPostPermission($request, $response)
    {
        if (!\ciab\RBAC::havePermission('api.set.artshow.configuration')) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, 'Permission Denied', 'Permission Denied', 403)];
        }
        return null;

    }


    /* End BaseConfiguration */
}
