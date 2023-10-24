<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Vendors\Zend;

use Zend\Permissions\Rbac\Rbac;
use Zend\Permissions\Rbac\Role;

class ZendRBACBackend implements \App\Vendors\Interfaces\RBACVendor
{

    /**
     * @var Permissions
     */
    protected $permissions = array();


    public function __construct()
    {
        $this->rbac = new Rbac();

    }


    public function positionHasPermission(
        /*.mixed.*/$pos,
        /*.string.*/$name
    ) {
        if (!in_array($name, $this->permissions)) {
            error_log("Unregistered RBAC permission ".$name);
        }
        try {
            return $this->rbac->getRole(
                strval($pos)
            )->hasPermission($name);
        } catch (Exception\InvalidArgumentException $e) {
            return false;
        }

    }


    public function getPermissions(
        /*.string.*/$role,
        /*.bool.*/ $children = true
    ) {
        try {
            $result = $this->rbac->getRole(
                strval($role)
            )->getPermissions($children);
            return array_unique($result);
        } catch (Exception\InvalidArgumentException $e) {
            return null;
        }

    }


    public function getAllPermissions()
    {
        return $this->permissions;

    }


    public function isPermission(/*.string.*/$permission): bool
    {
        return in_array($permission, $this->permissions);

    }


    public function registerPermissions(/*.mixed.*/$permissions)
    {
        if (is_array($permissions)) {
            foreach ($permissions as $p) {
                $this->addPermission($p);
            }
        } else {
            $this->addPermission($permissions);
        }

    }


    public function addRole(/*.string.*/$role, /*.array.*/$parents = null)
    {
        try {
            $r = new Role($role);
            $this->rbac->addRole($r, $parents);
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

    }


    public function removeRole(/*.string.*/$role)
    {
        /* No action in Vend/Laminas backend */

    }


    public function addRoleParents(/*.string.*/$role, /*.array.*/$parents)
    {
        try {
            $r = $this->rbac->getRole($role);
            if (!$r) {
                $this->addRole($role, $parents);
            } else {
                foreach ($parents as $parent) {
                    $p = $this->rbac->getRole($parent);
                    $r->addParent($p);
                }
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

    }


    public function removeRoleParent(/*.string.*/$role, /*.string.*/$parent)
    {
        /* No action in Vend/Laminas backend */

    }


    public function addPermission(/*.string.*/$permission)
    {
        if (!in_array($permission, $this->permissions)) {
            $this->permissions[] = $permission;
        }

    }


    public function removePermission(/*.string.*/$permission)
    {
        if (($key = array_search($permission, $this->permissions)) !== false) {
            unset($this->permissions[$key]);
        }

    }


    public function grantPermission(/*.string.*/$role, /*.string.*/$permission)
    {
        try {
            $r = $this->rbac->getRole($role);
            if ($r) {
                $r->addPermission($permission);
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

    }


    public function revokePermission(/*.string.*/$role, /*.string.*/$permission)
    {
        /* No action in Vend/Laminas backend */

    }


    /* end Rbac */
}
