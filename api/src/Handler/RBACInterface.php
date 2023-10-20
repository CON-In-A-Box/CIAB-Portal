<?php

/*.
    require_module 'standard';
.*/

namespace App\Handler;

interface RBACInterface
{


    public function install($container);


    public function havePermission(/*.string.*/ $name);


    public function getPermissions(/*.string.*/ $role, /*.bool.*/ $children = true);


    public function getAllPermissions();


    public function getMemberPermissions(/*.int.*/ $id);


    public function registerPermissions(/*.mixed.*/$permissions);


    public function addRole(/*.string.*/$role, /*.array.*/$parents = null);


    public function removeRole(/*.string.*/$role);


    public function addRoleParents(/*.string.*/$role, /*.array.*/$parents);


    public function removeRoleParent(/*.string.*/$role, /*.string.*/$parent);


    public function grantPermission(/*.string.*/$role, /*.string.*/$permission);


    public function revokePermission(/*.string.*/$role, /*.string.*/$permission);


    /* end class */
}
