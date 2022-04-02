<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Configuration;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Insert;

trait TraitPost
{

    /**
     * @var string
     */
    protected static $param = 'type';


    public function buildResource(Request $request, Response $response, $params): array
    {
        $this->checkPostPermission($request, $response);
        $body = $this->checkRequiredBody($request, static::$required);
        $insert = Insert::new($this->container->db)
            ->into(static::$table)
            ->columns(static::insertPayloadFromParams($body, false));
        $insert->perform();

        if (static::$get_id !== null) {
            $id = $body[static::$get_id];
        } else {
            $id = $insert->getLastInsertId();
        }

        $target = new static::$resource($this->container);
        $data = $target->buildResource($request, $response, [static::$param => $id])[1];
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        $target->arrayResponse($request, $response, $data),
        201
        ];

    }


    /* end TraitPost */
}
