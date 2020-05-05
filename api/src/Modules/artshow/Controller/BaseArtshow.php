<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\artshow\Controller;

use Slim\Container;
use App\Controller\BaseController;

abstract class BaseArtshow extends BaseController
{


    public function __construct(string $api_type, Container $container)
    {
        parent::__construct($api_type, $container);

    }


    protected function getEvent($params, $target)
    {
        if (array_key_exists($target, $params)) {
            return $params[$target];
        } else {
            return $this->currentEvent();
        }

    }


    protected function checkParameter($request, $response, $source, $field)
    {
        if ($source == null || !array_key_exists($field, $source)) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, "Required '$field' parameter not present", 'Missing Parameter', 400)];
        }
        return null;

    }


    protected function checkParameters($request, $response, $source, $fields)
    {
        if (is_array($fields)) {
            foreach ($fields as $field) {
                $result = $this->checkParameter($request, $response, $source, $field);
                if ($result !== null) {
                    return $result;
                }
            }
        } else {
            return $this->checkParameter($source, $fields);
        }
        return null;

    }


    protected function executePut($request, $response, $sql)
    {
        $sth = $this->container->db->prepare($sql);
        $sth->execute();
        if ($sth->rowCount() < 1) {
            return [
            \App\Controller\BaseController::RESULT_TYPE,
            $this->errorResponse($request, $response, "'$sql' updated 0 rows.", 'Put Failed', 400)];
        }

        return [null];

    }


    /* End BaseArtshow */
}
