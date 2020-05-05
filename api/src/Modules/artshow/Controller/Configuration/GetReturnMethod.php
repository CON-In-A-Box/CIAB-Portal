<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Configuration;

use Slim\Http\Request;
use Slim\Http\Response;

class GetReturnMethod extends BaseConfiguration
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $return_list = true;
        $sql = "SELECT * FROM `Artshow_ReturnMethod`";
        if (array_key_exists('method', $params)) {
            $sql .= " WHERE `ReturnMethod` = '{$params['method']}'";
            $return_list = false;
        }

        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll();
        if (!$return_list) {
            if (count($data) >= 1) {
                $output = [
                'type' => 'returnmethod',
                'method' => $data[0]['ReturnMethod'],
                ];
                return [
                \App\Controller\BaseController::RESOURCE_TYPE,
                $output];
            }
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse(
                $request,
                $response,
                'Not Found',
                'Return Method \''.$params['method'].'\' Not Found',
                404
            )];
        }
        $output = array();
        foreach ($data as $entry) {
            $output[] = [
            'type' => 'returnmethod_entry',
            'method' => $entry['ReturnMethod']
            ];
        }
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $output,
        array('type' => 'returnmethod_list')];

    }


    /* end GetReturnMethod */
}
