<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Configuration;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Update;
use App\Controller\InvalidParameterException;

trait TraitPut
{

    /**
     * @var string
     */
    protected static $param = 'type';


    public function buildResource(Request $request, Response $response, $params): array
    {
        $this->checkPutPermission($request, $response);
        $body = $request->getParsedBody();
        if (!$body) {
            throw new InvalidParameterException("Body required");
        }
        $update = Update::new($this->container->db)
            ->table(static::$table)
            ->columns(static::insertPayloadFromParams($body, false))
            ->whereEquals([static::$db_type => $params[static::$param]]);
        return $this->executePut($request, $response, $update);

    }


    /* end TraitPut */
}
