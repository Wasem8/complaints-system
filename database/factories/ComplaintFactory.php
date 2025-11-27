<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Complaint;

class ComplaintFactory extends Factory
{
    protected $model = Complaint::class;

    public function definition(): array
    {
        return [
            'user_id' => null, // will be set in seeder
            'department_id' => 1, // can be randomized in seeder
            'type' => $this->faker->randomElement([
                'service_missing',
                'power_outage',
                'corruption',
                'employee_misconduct',
                'technical_issue'
            ]),
            'description' => $this->faker->paragraph(),
            'location_text' => $this->faker->address(),
            'status' => 'pending',
            'tracking_number' => $this->faker->unique()->numerify('CMP-#####'),
        ];
    }
}
