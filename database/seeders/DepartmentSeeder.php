<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'الخدمة العامة', 'description' => 'الشكاوى المتعلقة بالخدمات العامة'],
            ['name' => 'الطاقة والكهرباء', 'description' => 'الشكاوى المتعلقة بانقطاع الكهرباء'],
            ['name' => 'الفساد', 'description' => 'الشكاوى المتعلقة بالفساد'],
            ['name' => 'سلوك الموظفين', 'description' => 'الشكاوى حول تصرفات الموظفين'],
            ['name' => 'المشاكل التقنية', 'description' => 'الشكاوى التقنية المختلفة'],
        ];

        foreach ($departments as $dep) {
            Department::create($dep);
        }
    }
}
