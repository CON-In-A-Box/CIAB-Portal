<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Stream;

use Slim\Http\Request;
use Slim\Http\Response;

trait StreamController
{


    abstract public function doWork(Request $request, Response $response, $args, $lastEventId): void;


    public function expandIncludes(Request $request, Response $response, $args, $data): array
    {
        $cleandata = array_values($data);
        if ($this->includes !== null) {
            $short = $request->getQueryParam('short_response', false);
            if (!boolval($short)) {
                foreach ($cleandata as $index => $entry) {
                    $this->processIncludes($request, $response, $args, $cleandata[$index]);
                }
            }
        } else {
            $cleandata = $data;
        }
        return $cleandata;

    }


    public function sendStreamPacket($id, string $data) : void
    {
        echo "id: $id\n";
        echo "data: $data\n\n";
        ob_flush();
        flush();

    }


    public function endStream() : void
    {
        echo "data: END\n\n";
        ob_flush();
        flush();

    }


    public function buildResource(Request $request, Response $response, $params): array
    {
        set_time_limit(300);
        return $this->buildStreamResource($request, $response, $params);

    }


    /* End */
}
