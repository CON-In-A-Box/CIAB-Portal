<?php declare(strict_types=1);
/*.
    require_module 'standard';
.*/

namespace App\Vendors\Interfaces;

interface RBACVendor
{


    public function positionHasPermission(
        /*.mixed.*/$pos,
        /*.string.*/$name
    );


    public function getPermissions(
        /*.string.*/$role,
        /*.bool.*/ $children = true
    );


    public function registerPermissions(/*.mixed.*/$permissions);


    public function addRole(/*.string.*/$role, /*.array.*/$parents = null);


    public function removeRole(/*.string.*/$role);


    public function addRoleParents(/*.string.*/$role, /*.array.*/$parents);


    public function removeRoleParent(/*.string.*/$role, /*.string.*/$parent);


    public function addPermission(/*.string.*/$permission);


    public function removePermission(/*.string.*/$permission);


    public function getAllPermissions();


    public function isPermission(/*.string.*/$permission): bool;


    public function grantPermission(/*.string.*/$role, /*.string.*/$permission);


    public function revokePermission(/*.string.*/$role, /*.string.*/$permission);


    /* end Rbac */
}
