<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Configuration;

use Slim\Http\Request;
use Slim\Http\Response;

class GetPriceType extends BaseConfiguration
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $return_list = true;
        $sql = "SELECT * FROM `Artshow_PriceType`";
        if (array_key_exists('type', $params)) {
            $sql .= " WHERE `PriceType` = '{$params['type']}'";
            $return_list = false;
        }
        $sql .= " ORDER BY `Position` ASC";

        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll();
        if (!$return_list) {
            if (count($data) >= 1) {
                $output = [
                'type' => 'pricetype',
                'priceType' => $data[0]['PriceType'],
                'position' => $data[0]['Position'],
                'settable' => $data[0]['SetPrice']
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
                'Price Type \''.$params['type'].'\' Not Found',
                404
            )];
        }
        $output = array();
        foreach ($data as $entry) {
            $output[] = [
            'type' => 'pricetype_entry',
            'priceType' => $entry['PriceType'],
            'position' => $entry['Position'],
            'settable' => $entry['SetPrice']
            ];
        }
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $output,
        array('type' => 'pricetype_list')];

    }


    /* end GetPriceType */
}
