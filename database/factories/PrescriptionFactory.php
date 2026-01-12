<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Examination;
use App\Models\Prescription;

class PrescriptionFactory extends Factory
{
    protected $model = Prescription::class;

    public function definition()
    {
        return [
            'examination_id' => Examination::factory(),
            
            'doctor_id' => function (array $attrs) {
                return Examination::find($attrs['examination_id'])->doctor_id;
            },
            
            'examined_at' => function (array $attrs) {
                return Examination::find($attrs['examination_id'])->examined_at;
            },

            'status' => 'pending',
        ];
    }
}
