<?php

namespace App\Repositories\Eloquent;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Repositories\Contracts\PermissionRepositoryInterface;

class PermissionRepository implements PermissionRepositoryInterface
{
    public function allRoles()
    {
        return Role::with('permissions')->get();
    }

    public function allPermissions()
    {
        return Permission::all();
    }

    public function createRole(string $name)
    {
        return Role::create(['name' => $name, 'guard_name' => 'api']);
    }

    public function createPermission(string $name)
    {
        return Permission::create(['name' => $name, 'guard_name' => 'api']);
    }

    public function assignPermissions(string $role, array $permissions)
    {
        $roleObj = Role::where('name', $role)->first();
        return $roleObj->syncPermissions($permissions);
    }
}
