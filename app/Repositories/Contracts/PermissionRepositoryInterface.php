<?php

namespace App\Repositories\Contracts;

interface PermissionRepositoryInterface
{
    public function allRoles();
    public function allPermissions();
    public function createRole(string $name);
    public function createPermission(string $name);
    public function assignPermissions(string $role, array $permissions);
}
