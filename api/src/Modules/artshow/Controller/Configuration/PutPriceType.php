<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Configuration;

use Slim\Http\Request;
use Slim\Http\Response;

class PutPriceType extends BaseConfiguration
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $check = $this->checkPutPermission($request, $response);
        $type = urldecode($params['type']);
        $body = $request->getParsedBody();

        $fields = array();
        foreach ($body as $key => $value) {
            $key = str_replace('_', ' ', $key);
            $fields[] = " `$key` = ".\MyPDO::quote($value);
        }

        $sql = "UPDATE `Artshow_PriceType` SET ";
        $sql .= implode(", ", $fields);
        $sql .= "WHERE `PriceType` = '$type'";

        return $this->executePut($request, $response, $sql);

    }


    /* end PutPriceType */
}
