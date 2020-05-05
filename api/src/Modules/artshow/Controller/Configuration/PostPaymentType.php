<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Configuration;

use Slim\Http\Request;
use Slim\Http\Response;

class PostPaymentType extends BaseConfiguration
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $check = $this->checkPostPermission($request, $response);
        if ($check != null) {
            return $check;
        }
        $body = $request->getParsedBody();
        $check = $this->checkParameters($request, $response, $body, ['Type']);
        if ($check !== null) {
            return $check;
        }
        $newType = $body['Type'];
        $output = array();
        $sth = $this->container->db->prepare("INSERT INTO `Artshow_PaymentType` (`PaymentType`) VALUES ('$newType')");
        $sth->execute();
        return [null];

    }


    /* end PostPaymentType */
}
