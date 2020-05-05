<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Configuration;

use Slim\Http\Request;
use Slim\Http\Response;

class PostRegistrationQuestion extends BaseConfiguration
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $check = $this->checkPostPermission($request, $response);
        if ($check != null) {
            return $check;
        }
        $body = $request->getParsedBody();
        $check = $this->checkParameters($request, $response, $body, ['Text']);
        if ($check !== null) {
            return $check;
        }
        $fields = array();

        foreach ($body as $key => $value) {
            $key = str_replace('_', ' ', $key);
            $fields[$key] = \MyPDO::quote($value);
        }

        $sql = "INSERT INTO `Artshow_RegistrationQuestion` (";
        $sql .= implode(", ", array_keys($fields));
        $sql .= ") VALUES (";
        $sql .= implode(", ", array_values($fields));
        $sql .= ")";

        $output = array();
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        return [null];

    }


    /* end PostRegistrationQuestion */
}
