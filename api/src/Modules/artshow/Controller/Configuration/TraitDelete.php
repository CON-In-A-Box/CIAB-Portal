<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Configuration;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Delete;
use App\Controller\NotFoundException;

trait TraitDelete
{

    /**
     * @var string
     */
    protected static $param = 'type';


    public function buildResource(Request $request, Response $response, $params): array
    {
        $result = Delete::new($this->container->db)
            ->from(static::$table)
            ->whereEquals([static::$db_type => $params[static::$param]])
            ->perform();
        if ($result->rowCount() == 0) {
            throw new NotFoundException("Entry '".$params[static::$param]."' Not Found in ".static::$table);
        }
        return [
        \App\Controller\BaseController::RESOURCE_TYPE,
        [null],
        204
        ];

    }


    /* End TraitDelete */
}
