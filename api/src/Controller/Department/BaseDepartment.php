<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Controller\Department;

use Slim\Container;
use Slim\Http\Request;
use App\Controller\BaseController;

abstract class BaseDepartment extends BaseController
{

    /**
     * @var int
     */
    protected $id = 0;

    /**
     * @var int
     */
    protected $division = 0;


    public function __construct(Container $container)
    {
        parent::__construct('department', $container);

    }


    protected function buildDepartmentGet($request, $id)
    {
        $path = $request->getUri()->getBaseUrl();
        return ($path.'/department/'.strval($id));

    }


    public function getDepartment($id, $setself = true)
    {
        $output = parent::getDepartment($id);
        if ($setself) {
            if (!empty($output)) {
                if (array_key_exists('id', $output)) {
                    $this->id = $output['id'];
                }
                if (array_key_exists('Division', $output) &&
                    $output['Division'] !== $output['Name']) {
                    $this->division = $this->getDepartment($output['Division'], false)['id'];
                }
            }
        }
        return $output;

    }


    /* End BaseDepartment */
}
