<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller\Configuration;

use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;
use App\Controller\NotFoundException;

trait TraitGet
{

    protected $parameter = 'type';


    public function buildResource(Request $request, Response $response, $params): array
    {
        $return_list = true;
        $select = Select::new($this->container->db)
            ->columns(...static::selectMapping())
            ->from(static::$table);
        if (static::$db_type !== null &&
            array_key_exists($this->parameter, $params)) {
            $select->whereEquals([static::$db_type => $params[$this->parameter]]);
            $return_list = false;
        }
        $data = $select->perform()->fetchAll();

        if (!$return_list) {
            if (count($data) >= 1) {
                return [
                \App\Controller\BaseController::RESOURCE_TYPE,
                $data[0]];
            }
            throw new NotFoundException("'{$params['type']}' Not Found");
        }
        return [
        \App\Controller\BaseController::LIST_TYPE,
        $data,
        array('type' => static::$list_type)];

    }


    /* end TraitGet */
}
