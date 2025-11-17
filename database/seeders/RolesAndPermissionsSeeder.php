<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ///crete roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $citizenRole = Role::firstOrCreate(['name' => 'citizen']);
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);

        ///create permissions
        $permissions = [
            // Citizen
            'submit complaint',
            'view own complaints',
            'update profile',
            'close complaint',

            // Employee
            'view department complaints',
            'change complaint status',
            'add employee note',
            'reply to citizen',
            'archive complaint',

            // Admin
            'view all complaints',
            'manage users',
            'manage employees',
            'manage roles',
            'manage departments',
            'system settings',
            'view analytics',
            'export reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $citizenPermisson = [
            'submit complaint',
            'view own complaints',
            'update profile',
            'close complaint',
        ];

        $employeePermisson = [
            'view department complaints',
            'change complaint status',
            'add employee note',
            'reply to citizen',
            'archive complaint',
        ];

        ///Assign permissions to roles
        $adminRole->syncPermissions(Permission::all());
        $citizenRole->syncPermissions($citizenPermisson);
        $employeeRole->syncPermissions($employeePermisson);

    }
}
