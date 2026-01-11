<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prescription;

class PharmacistPrescriptionController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Prescription::class);

        $prescriptions = Prescription::where('status', 'pending')->get();

        return response()->json($prescriptions);
    }

    public function calculatePrice($id)
    {
        $prescription = Prescription::findOrFail($id);

        $this->authorize('calculate', $prescription);

        if (! $prescription->isPending()) {
            return response()->json([
                'error' => 'Prescription status must be pending to calculate price'
            ], 400);
        }

        $prescription->total_price = 100_000;

        $prescription->status = 'calculated';
        $prescription->save();

        return response()->json([
            'message' => 'Price calculated',
            'prescription' => $prescription
        ]);
    }

    public function lock($id)
    {
        $prescription = Prescription::findOrFail($id);

        $this->authorize('lock', $prescription);

        if (! $prescription->isPaid()) {
            return response()->json([
                'error' => 'Prescription must be paid before locking'
            ], 400);
        }

        $prescription->status = 'locked';
        $prescription->locked_at = now();
        $prescription->save();

        return response()->json([
            'message' => 'Prescription locked',
            'prescription' => $prescription
        ]);
    }
}
