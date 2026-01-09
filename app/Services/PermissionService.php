<?php

namespace App\Services;

use App\Repositories\Contracts\PermissionRepositoryInterface;

class PermissionService
{
    protected PermissionRepositoryInterface $permissions;

    public function __construct(PermissionRepositoryInterface $permissions)
    {
        $this->permissions = $permissions;
    }

    public function roles()
    {
        return $this->permissions->allRoles();
    }

    public function permissions()
    {
        return $this->permissions->allPermissions();
    }


    public function createRoleWithPermissions(string $roleName, array $permissions)
    {

        $existingPermissions = $this->permissions->allPermissions()->pluck('name')->toArray();

        $invalidPermissions = array_diff($permissions, $existingPermissions);

        if (!empty($invalidPermissions)) {
            throw new \InvalidArgumentException('The following permissions do not exist: ' . implode(', ', $invalidPermissions));
        }

        $role = $this->permissions->createRole($roleName);


        if (!empty($permissions)) {
            $this->permissions->assignPermissions($roleName, $permissions);
        }


        return $this->permissions->allRoles()->where('name', $roleName)->first();
    }

    public function createRole(string $name)
    {
        return $this->permissions->createRole($name);
    }

    public function createPermission(string $name)
    {
        return $this->permissions->createPermission($name);
    }

    public function assignPermissions(string $role, array $permissions)
    {
        return $this->permissions->assignPermissions($role, $permissions);
    }
}
