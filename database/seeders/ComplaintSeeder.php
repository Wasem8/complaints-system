<?php

namespace Database\Seeders;

use App\Models\Complaint;
use App\Models\Department;
use App\Models\User;
use App\Models\Complaint_status_log;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ComplaintSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereNotNull('department_id')->get();

        if ($users->isEmpty()) {
            $this->command->warn('No users with department found.');
            return;
        }

        $faker = \Faker\Factory::create();

        $citizens = User::role('citizen')->get(); // فقط المواطنين
        $departments = Department::all();

        $types = [
            'service_missing',
            'power_outage',
            'corruption',
            'employee_misconduct',
            'technical_issue',
        ];

            for ($i = 1; $i <= 20; $i++) {
                $citizen = $citizens->random();
                $department = $departments->random();

                $complaint =  Complaint::create([
                    'user_id' => $citizen->id,
                    'department_id' => $department->id,
                    'type' => $faker->randomElement($types),
                    'description' => $faker->paragraph(3),
                    'location_text' => $faker->address(),
                    'status' => 'pending',
                    'tracking_number' => 'CMP-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6)),

                ]);

            Complaint_status_log::create([
                'complaint_id' => $complaint->id,
                'new_status' => 'pending',
                'note' => 'Complaint created by seeder',
            ]);
        }

        $this->command->info('✅ 20 complaints seeded successfully.');
    }
}
