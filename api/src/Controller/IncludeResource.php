<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class IncludeResource
{

    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $parameter;


    public function __construct(string $class, string $parameter, string $field)
    {
        $this->class = $class;
        $this->field = $field;
        $this->parameter = $parameter;

    }


    private function hasIncludeData($data) : bool
    {
        return (in_array($this->field, array_keys($data), true) && $data[$this->field] !== null);

    }


    private function collectResourceData(Request $request, Response $response, $target, array $params, array $data)
    {
        $newparams = $params;
        $newparams[$this->parameter] = $data[$this->field];

        /* Call the buildResource for the included resource */
        $newdata = $target->buildResource($request, $response, $newparams);

        return $newdata[1];

    }


    public function process(Request $request, Response $response, Container $container, array $params, array &$data, array $history): void
    {
        if ($this->hasIncludeData($data)) {
            /* Check if this resource class is in the history and if not add a blank history entry for this class */
            if (!array_key_exists($this->class, $history)) {
                $history[$this->class] = [];
            }

            /* check if in the class history this field has already been looked up. If so we do not continue */
            if (in_array($data[$this->field], $history[$this->class])) {
                return;
            }

            $target = new $this->class($container);
            try {
                $newdata = $this->collectResourceData($request, $response, $target, $params, $data);

                /* Add this field to the history */
                $history[$this->class][] = $data[$this->field];

                /* Process all the includes for the newly included resource (Recursion) */
                IncludeResource::processIncludes($target->includes, $request, $response, $container, $params, $newdata, $history);

                /* Add all the response to the data for the resource */
                $data[$this->field] = $target->arrayResponse($request, $response, $newdata);
            } catch (Exception $e) {
                return;
            }
        }

    }


    public static function processIncludes($includes, Request $request, Response $response, Container $container, $params, &$data, array $history = [])
    {
        if ($includes !== null) {
            $short = $request->getQueryParam('short_response', false);
            if (!boolval($short)) {
                foreach ($includes as $target) {
                    $target->process($request, $response, $container, $params, $data, $history);
                }
            }
        }

    }


    /* End IncludeResource */
}
