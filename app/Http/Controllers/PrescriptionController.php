<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prescription;
use App\Models\Examination;

class PrescriptionController extends Controller
{
    public function createFromExamination($examinationId)
    {
        $this->authorize('create', Prescription::class);

        $examination = Examination::findOrFail($examinationId);

        if ($examination->status !== 'completed') {
            return response()->json([
                'error' => 'Examination must be completed before creating prescription'
            ], 400);
        }

        if ($examination->prescription) {
            return response()->json([
                'error' => 'Prescription already exists for this examination'
            ], 400);
        }

        return response()->json([
            'examination' => $examination,
            'message' => 'Ready to create prescription'
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Prescription::class);

        $request->validate([
            'examination_id' => 'required|exists:examinations,id'
        ]);

        $examination = Examination::findOrFail($request->examination_id);

        if ($examination->status !== 'completed') {
            return response()->json([
                'error' => 'Examination must be completed before creating prescription'
            ], 400);
        }

        if ($examination->prescription) {
            return response()->json([
                'error' => 'Prescription already exists for this examination'
            ], 400);
        }

        $prescription = Prescription::create([
            'examination_id' => $request->examination_id,
            'doctor_id' => auth()->id(),
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Prescription created successfully',
            'data' => $prescription
        ], 201);
    }
}
