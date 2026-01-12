<?php

namespace Database\Factories;

use App\Models\Prescription;
use App\Models\PrescriptionItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrescriptionItemFactory extends Factory
{
    protected $model = PrescriptionItem::class;

    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 5);
        $unitPrice = $this->faker->numberBetween(1000, 5000);

        return [
            'prescription_id' => Prescription::factory(),
            'medicine_id'     => $this->faker->uuid(),
            'medicine_name'   => $this->faker->randomElement([
                'Paracetamol 500mg', 
                'Amoxicillin 500mg', 
                'Cetirizine 10mg', 
                'Ibuprofen 400mg'
            ]),
            'quantity'        => $quantity,
            'unit_price'      => $unitPrice,
            'total_price'     => $quantity * $unitPrice, 
        ];
    }
}