<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Configuration;

use Slim\Http\Request;
use Slim\Http\Response;

class GetRegistrationQuestion extends BaseConfiguration
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $return_list = true;
        $sql = "SELECT * FROM `Artshow_RegistrationQuestion`";
        if (array_key_exists('id', $params)) {
            $sql .= " WHERE `QuestionID` = '{$params['id']}'";
            $return_list = false;
        }

        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll();
        if (!$return_list) {
            if (count($data) >= 1) {
                $output = [
                'type' => 'registrationquestion',
                'id' => $data[0]['QuestionID'],
                'boolean' => $data[0]['BooleanQuestion'],
                'text' => $data[0]['Text']
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
                'Registration Question \''.$params['id'].'\' Not Found',
                404
            )];
        }
        $output = array();
        foreach ($data as $entry) {
            $output[] = [
            'type' => 'registrationquestion_entry',
            'id' => $entry['QuestionID'],
            'boolean' => $entry['BooleanQuestion'],
            'text' => $entry['Text']
            ];
        }
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $output,
        array('type' => 'registrationquestion_list')];

    }


    /* end GetRegistrationQuestion */
}
