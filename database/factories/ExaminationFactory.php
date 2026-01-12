<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Examination;
use App\Models\Patient;
use App\Models\User;

class ExaminationFactory extends Factory
{
    protected $model = Examination::class;

    public function definition()
    {
        return [
            'doctor_id' => User::factory()->state(['role' => 'doctor']),
            'patient_id' => Patient::factory(),
            'status' => 'completed',
            'examined_at' => now(),
            'height' => $this->faker->numberBetween(150, 190),
            'weight' => $this->faker->numberBetween(50, 100),
            'systole' => $this->faker->numberBetween(110, 140),
            'diastole' => $this->faker->numberBetween(70, 90),
            'heart_rate' => $this->faker->numberBetween(60, 100),
            'respiration_rate' => $this->faker->numberBetween(16, 24),
            'temperature' => $this->faker->randomFloat(1, 36, 38),
            'notes' => $this->faker->paragraph(2), 
            'attachment_path' => null,
        ];
    }
}