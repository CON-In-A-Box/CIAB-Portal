<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Modules\volunteers;

use App\Modules\BaseModule;
use Slim\Http\Request;
use Slim\Http\Response;
use Atlas\Query\Select;

class ModuleVolunteers extends BaseModule
{


    public function __construct($source)
    {
        parent::__construct($source);
        \ciab\RBAC::customizeRBAC('\App\Modules\volunteers\ModuleVolunteers::customizeAnnouncementRBAC');

    }


    public function valid()
    {
        return true;

    }


    public function handle(Request $request, Response $response, $data, $code)
    {
        return $data;

    }


    public function customizeAnnouncementRBAC($instance, $database)
    {
        $role = $instance->getRole('all.staff');
        $role->addPermission('api.post.volunteers');
        $role->addPermission('api.get.volunteer.hours');

        $positions = [];
        $values = Select::new($database)
            ->columns('PositionID', 'Name')
            ->from('ConComPositions')
            ->orderBy('`PositionID` ASC')
            ->fetchAll();
        foreach ($values as $value) {
            $positions[intval($value['PositionID'])] = $value['Name'];
        }

        $values = Select::new($database)
            ->columns('DepartmentID')
            ->from('Departments')
            ->whereEquals(['Name' => 'Volunteers'])
            ->fetchOne();

        $target_h = $values['DepartmentID'].'.'.array_keys($positions)[0];
        try {
            $role = $instance->getRole($target_h);
            $role->addPermission('api.get.volunteer.claims');
            $role->addPermission('api.put.volunteers');
            $role->addPermission('api.delete.volunteers');
            $role->addPermission('api.post.volunteers.admin');
        } catch (Exception\InvalidArgumentException $e) {
            error_log($e);
        }

    }


    /* End ModuleVolunteers */
}
