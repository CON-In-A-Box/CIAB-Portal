<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\concom;

use App\Modules\BaseModule;
use Slim\Http\Request;
use Slim\Http\Response;

class ModuleConcom extends BaseModule
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
        if (get_class($this->source) == 'App\Controller\Member\GetMember') {
            if (array_key_exists('id', $data)) {
                $id = $data['id'];
                $concom = \concom\POSITION::getConComPosition($id);
                if (!empty($concom)) {
                    $this->source->addHateoasLink('concom', 'member/'.$id.'/concom', 'GET');
                }
            }
        }
        if (get_class($this->source) == 'App\Controller\Department\GetDepartment') {
            if (array_key_exists('id', $data)) {
                $id = $data['id'];
                $this->source->addHateoasLink('concom', 'department/'.$id.'/concom', 'GET');
            }
        }
        return $data;

    }


    /* End ModuleConcom */
}
