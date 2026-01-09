<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        $departments = Department::all();

        // Create example admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // Create 5 employees
        for ($i = 1; $i <= 5; $i++) {
            $employee = User::firstOrCreate(
                ['email' => "employee{$i}@example.com"],
                [
                    'name' => $faker->name(),
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                    'department_id' => $departments->random()->id, // Random department
                ]
            );
            $employee->assignRole('employee');
        }

        // Create 10 citizens
        for ($i = 1; $i <= 10; $i++) {
            $citizen = User::firstOrCreate(
                ['email' => "citizen{$i}@example.com"],
                [
                    'name' => $faker->name(),
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                ]
            );
            $citizen->assignRole('citizen');
        }
    }
}
