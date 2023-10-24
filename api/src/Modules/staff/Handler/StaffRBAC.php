<?php
/*.
    require_module 'standard';
.*/

namespace App\Modules\staff\Handler;

use Atlas\Query\Select;
use App\Controller\BaseController;
use App\Modules\staff\Controller\BaseStaff;

class StaffRBAC implements \App\Handler\RBACInterface
{

    /**
     * @var backend
     */
    protected $backend = null;

    /**
     * @var container
     */
    protected $container = null;


    public function __construct()
    {
        $this->backend = new \App\Vendors\Zend\ZendRBACBackend();

    }


    private function getStaffPosition($accountId)
    {
        $event = BaseController::staticGetEvent($this->container, 'current')['id'];
        return BaseStaff::staticSelectStaff($this->container, $event, null, $accountId);

    }


    public function install($container)
    {
        $this->container = $container;
        $this->addRole('admin');
        $this->addRole('all.staff', 'admin');

        $positions = [];
        $data = Select::new($container->db)
            ->columns('PositionID')
            ->from('ConComPositions')
            ->orderBy('`PositionID` ASC')
            ->fetchAll();
        $parent = 'admin';
        foreach ($data as $value) {
            $positions[] = $value['PositionID'];
            $new = 'all.'.$value['PositionID'];
            $this->addRole($new, [$parent]);
            $parent = $new;
        }

        $data = Select::new($container->db)
            ->columns('DepartmentID')
            ->from('Departments')
            ->whereEquals(['ParentDepartmentID', 'DepartmentID'])
            ->fetchAll();
        foreach ($data as $value) {
            $parent = 'admin';
            foreach ($positions as $pos) {
                $new = $value['DepartmentID'].'.'.$pos;
                $this->addRole($new, [$parent]);
                $this->addToAll('all.staff', $new, $pos);
                $parent = $new;
            }
        }

        $data = Select::new($container->db)
            ->columns('ParentDepartmentID, DepartmentID')
            ->from('Departments')
            ->where('ParentDepartmentID != DepartmentID')
            ->fetchAll();
        foreach ($data as $value) {
            $firstparent = $value['ParentDepartmentID'].'.'.end($positions);
            $parent = $firstparent;
            foreach ($positions as $pos) {
                $new = $value['DepartmentID'].'.'.$pos;
                $this->addRole($new, [$parent]);
                $this->addToAll('all.staff', $new, $pos);
                if ($parent == $firstparent) {
                    $parent = $new;
                }
            }
        }

        /* Load Permissions */
        $sql = "SELECT * FROM `ConComPermissions`";
        $data = Select::new($container->db)
            ->columns('Position, Permission')
            ->from('ConComPermissions')
            ->fetchAll();
        $retvalue = [];
        foreach ($data as $value) {
            try {
                $this->grantPermission(strval($value['Position']), $value['Permission']);
            } catch (Exception\InvalidArgumentException $e) {
            }
            $this->backend->addPermission($value['Permission']);
        }

    }


    private function addToAll(
        /*.string.*/$staff,
        /*.string.*/$new_role,
        /*.string.*/$pos
    ) {
        $this->addRoleParents($staff, [$new_role]);
        $this->addRoleParents("all.$pos", [$new_role]);

    }


    public function positionHasPermission(
        /*.mixed.*/$pos,
        /*.string.*/$name
    ) {
        return $this->backend->positionHasPermission($pos, $name);

    }


    public function getAllPermissions()
    {
        return $this->backend->getAllPermissions();

    }


    public function hasPermissions(
        /*.mixed.*/$accountId,
        /*.string.*/$name
    ) {
        if (!$this->backend->isPermission($name)) {
            error_log("Unregistered RBAC permission ".$name);
        }
        if (array_key_exists('IS_ADMIN', $_SESSION) && $_SESSION['IS_ADMIN']) {
            return true;
        }
        $positions = $this->getStaffPosition($accountId);
        foreach ($positions as $pos) {
            $value = $pos['department'].'.'.$pos['positionId'];
            if ($this->positionHasPermission($value, $name)) {
                return true;
            }
        }
        return false;

    }


    public function havePermission(/*.string.*/$name)
    {
        return $this->hasPermissions($_SESSION['accountId'], $name);

    }


    public function getPermissions(
        /*.string.*/$role,
        /*.bool.*/ $children = true
    ) {
        return $this->backend->getPermissions($role, $children);

    }


    public function getMemberPermissions(
        /*.int.*/$accountId
    ) : array {
        $result = [];
        if (array_key_exists('IS_ADMIN', $_SESSION) && $_SESSION['IS_ADMIN']) {
            return $this->getAllPermissions();
        }
        $positions = $this->getStaffPosition($accountId);
        foreach ($positions as $pos) {
            $role = $pos['department'].'.'.$pos['positionId'];
            try {
                $result = array_unique(array_merge($result, $this->getPermissions(
                    strval($role),
                    true
                )));
            } catch (Exception $e) {
            }
        }
        return $result;

    }


    public function registerPermissions(/*.mixed.*/$permissions)
    {
        return $this->backend->registerPermissions($permissions);

    }


    public function addRole(/*.string.*/$role, /*.array.*/$parents = null)
    {
        return $this->backend->addRole($role, $parents);

    }


    public function removeRole(/*.string.*/$role)
    {
        return $this->backend->removeRole($role);

    }


    public function addRoleParents(/*.string.*/$role, /*.array.*/$parents)
    {
        return $this->backend->addRoleParents($role, $parents);

    }


    public function removeRoleParent(/*.string.*/$role, /*.string.*/$parent)
    {
        return $this->backend->removeRoleParent($role);

    }


    public function grantPermission(/*.string.*/$role, /*.string.*/$permission)
    {
        return $this->backend->grantPermission($role, $permission);

    }


    public function revokePermission(/*.string.*/$role, /*.string.*/$permission)
    {
        return $this->backend->revokePermission($role, $permission);

    }


    /* end RBAC */
}
