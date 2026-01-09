<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'General Complaints',
                'description' => 'Department handling general complaints from citizens.'
            ],
            [
                'name' => 'Customer Service',
                'description' => 'Department for handling citizen inquiries.'
            ],
            [
                'name' => 'Technical Issues',
                'description' => 'Department dealing with technical complaints.'
            ],
            [
                'name' => 'Billing & Payments',
                'description' => 'Department handling billing and payment issues.'
            ],
            [
                'name' => 'Public Relations',
                'description' => 'Department managing public communications and announcements.'
            ],
            [
                'name' => 'Legal Affairs',
                'description' => 'Department dealing with legal and compliance matters.'
            ],
            [
                'name' => 'IT Support',
                'description' => 'Department providing technical support and troubleshooting.'
            ],
            [
                'name' => 'Human Resources',
                'description' => 'Department managing employee-related matters.'
            ],
            [
                'name' => 'Health & Safety',
                'description' => 'Department responsible for workplace safety and health issues.'
            ],
            [
                'name' => 'Research & Development',
                'description' => 'Department focused on improving services and innovation.'
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
