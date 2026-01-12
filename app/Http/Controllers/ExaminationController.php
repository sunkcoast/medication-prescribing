<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Examination;

class ExaminationController extends Controller
{
    public function store(Request $request)
    {
        $this->authorize('create', Examination::class);

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $examination = Examination::create([
            'doctor_id' => auth()->id(),
            'patient_id' => $request->patient_id,
            'notes' => $request->notes,
            'status' => 'new',
        ]);

        return response()->json($examination, 201);
    }

    public function uploadAttachment($id, Request $request)
    {
        $examination = Examination::findOrFail($id);

        $this->authorize('update', $examination);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');

            $request->validate([
                'attachment' => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
            ]);

            $path = $file->store('attachments', 'public');
            $examination->attachment_path = $path;
            $examination->save();
        } else {

            return response()->json([
                'error' => 'No attachment uploaded'
            ], 400);
        }
    
        return response()->json($examination);
    }
    
}
