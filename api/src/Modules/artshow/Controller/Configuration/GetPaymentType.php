<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Configuration;

use Slim\Http\Request;
use Slim\Http\Response;

class GetPaymentType extends BaseConfiguration
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $return_list = true;
        $sql = "SELECT * FROM `Artshow_PaymentType`";
        if (array_key_exists('type', $params)) {
            $sql .= " WHERE `PaymentType` = '{$params['type']}'";
            $return_list = false;
        }

        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll();
        if (!$return_list) {
            if (count($data) >= 1) {
                $output = [
                'type' => 'paymenttype',
                'paymentType' => $data[0]['PaymentType'],
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
                'Payment Type \''.$params['type'].'\' Not Found',
                404
            )];
        }
        $output = array();
        foreach ($data as $entry) {
            $output[] = [
            'type' => 'paymenttype_entry',
            'paymentType' => $entry['PaymentType'],
            ];
        }
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $output,
        array('type' => 'paymenttype_list')];

    }


    /* end GetPaymentType */
}
