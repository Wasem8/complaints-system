<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Complaint;
use App\Models\Complaint_file;
use App\Models\Complaint_status_log;
use App\Models\ComplaintFile;
use App\Models\ComplaintStatusLog;
use Illuminate\Support\Str;

class ComplaintSeeder extends Seeder
{
    public function run(): void
    {
        // Get all users with the role 'employee'
        $employees = User::role('employee')->get();

        if ($employees->isEmpty()) {
            $this->command->info('No users with employee role found!');
            return;
        }

        $employees->each(function ($user) {
            // Each employee has 2 complaints
            Complaint::factory(2)->create([
                'user_id' => $user->id,
                'department_id' => rand(1, 5), // random department
            ])->each(function ($complaint) use ($user) {

                // Add 1-2 files per complaint
                for ($i = 0; $i < rand(1, 2); $i++) {
                    Complaint_file::create([
                        'complaint_id' => $complaint->id,
                        'file_path' => 'uploads/dummy-file-' . Str::random(5) . '.jpg',
                        'file_type' => 'image',
                    ]);
                }

                // Randomly update status
                $statuses = ['processing', 'done', 'rejected'];
                $newStatus = $statuses[array_rand($statuses)];

                // Update complaint status
                $complaint->status = $newStatus;
                $complaint->save();

                // Log status update
                Complaint_status_log::create([
                    'complaint_id' => $complaint->id,
                    'new_status' => $newStatus,
                    'note' => 'تم تحديث الحالة تلقائياً بواسطة Seeder',
                ]);
            });
        });
    }
}
