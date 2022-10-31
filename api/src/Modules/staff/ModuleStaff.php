<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\staff;

use App\Modules\BaseModule;
use Slim\Http\Request;
use Slim\Http\Response;

class ModuleStaff extends BaseModule
{


    public function __construct($source)
    {
        parent::__construct($source);

    }


    public function valid()
    {
        if ($this->source !== null) {
            if (get_class($this->source) === 'App\Controller\Member\GetMember' ||
                get_class($this->source) === 'App\Controller\Department\GetDepartment') {
                return true;
            }
        }
        return false;

    }


    public function handle(Request $request, Response $response, $data, $code)
    {
        return $data;

    }


    /* End ModuleStaff */
}
