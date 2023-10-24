<?php

/*.
    require_module 'standard';
.*/

namespace App\Handler;

class RBAC
{

    /**
     * @var App\Handler\RBACInstance
     */
    protected $instance = null;

    protected $custom = array();


    public function __construct()
    {
        $this->instance = new \App\Modules\staff\Handler\StaffRBAC();

    }


    public function install($container)
    {
        if ($this->instance !== null) {
            return $this->instance->install($container);
        }

    }


    public function reload()
    {
        if ($this->instance !== null) {
            if (!empty($this->custom)) {
                if ($this->instance !== null) {
                    foreach ($this->custom as $entry) {
                        return $this->customizeRBAC($entry);
                    }
                }
            }
        }

    }


    public function havePermission(/*.string.*/ $name)
    {
        if ($this->instance !== null) {
            return $this->instance->havePermission($name);
        } else {
            return (array_key_exists('IS_ADMIN', $_SESSION) &&
                    $_SESSION['IS_ADMIN']);
        }

    }


    public function getPermissions(
        /*.string.*/ $role,
        /*.bool.*/ $children = true
    ) {
        if ($this->instance !== null) {
            return $this->instance->getPermissions($role, $children);
        } else {
            return null;
        }

    }


    public function getAllPermissions()
    {
        if ($this->instance !== null) {
            return $this->instance->getAllPermissions();
        } else {
            return null;
        }

    }


    public function getMemberPermissions(
        /*.int.*/ $id
    ) {
        if ($this->instance !== null) {
            return $this->instance->getMemberPermissions($id);
        } else {
            return null;
        }

    }


    public function customizeRBAC($entry)
    {
        $param = $this;
        try {
            @\call_user_func($entry, $param, \DB::instance());
        } catch (\Exception $e) {
            error_log($e);
        }

    }


    public function registerPermissions(/*.mixed.*/$permissions)
    {
        if ($this->instance !== null) {
            return $this->instance->registerPermissions($permissions);
        }

    }


    public function addRole(/*.string.*/$role, /*.array.*/$parents = null)
    {
        if ($this->instance !== null) {
            return $this->instance->addRole($role, $parents);
        }

    }


    public function removeRole(/*.string.*/$role)
    {
        if ($this->instance !== null) {
            return $this->instance->removeRole($role);
        }

    }


    public function addRoleParents(/*.string.*/$role, /*.array.*/$parents)
    {
        if ($this->instance !== null) {
            return $this->instance->addRoleParents($role, $parents);
        }

    }


    public function removeRoleParent(/*.string.*/$role, /*.string.*/$parent)
    {
        if ($this->instance !== null) {
            return $this->instance->removeRoleParent($role, $parent);
        }

    }


    public function grantPermission(/*.string.*/$role, /*.string.*/$permission)
    {
        if ($this->instance !== null) {
            return $this->instance->grantPermission($role, $permission);
        }

    }


    public function revokePermission(/*.string.*/$role, /*.string.*/$permission)
    {
        if ($this->instance !== null) {
            return $this->instance->revokePermission($role, $permission);
        }

    }


    /* end class */
}
