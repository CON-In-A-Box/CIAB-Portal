<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Configuration;

use Slim\Http\Request;
use Slim\Http\Response;

class PutPieceType extends BaseConfiguration
{


    public function buildResource(Request $request, Response $response, $params): array
    {
        $check = $this->checkPutPermission($request, $response);
        $type = urldecode($params['type']);
        $body = $request->getParsedBody();
        $check = $this->checkParameters($request, $response, $body, ['Type']);
        if ($check !== null) {
            return $check;
        }
        $newType = $body['Type'];
        $sql = "UPDATE `Artshow_PieceType` SET `PieceType` = '$newType' WHERE `PieceType` = '$type'";
        return $this->executePut($request, $response, $sql);

    }


    /* end PutPieceType */
}
