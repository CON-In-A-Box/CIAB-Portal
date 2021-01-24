<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\registration;

use App\Modules\BaseModule;
use Slim\Http\Request;
use Slim\Http\Response;

class ModuleRegistration extends BaseModule
{


    public function __construct($source)
    {
        parent::__construct($source);

    }


    public function valid()
    {
        return true;

    }


    public function handle(Request $request, Response $response, $data, $code)
    {
        return $data;

    }


    /* End ModuleRegistration */
}
