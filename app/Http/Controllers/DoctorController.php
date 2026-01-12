<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Examination;

class DoctorController extends Controller
{
    // Fitur 3a: Menyimpan Pemeriksaan
    public function storeExamination(Request $request)
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
            'status' => 'completed',
            'examined_at' => now(),
        ]);

        return response()->json($examination, 201);
    }

    // Fitur 3a: Upload Berkas
    public function uploadAttachment($id, Request $request)
    {
        $examination = Examination::findOrFail($id);
        $this->authorize('update', $examination);

        if ($request->hasFile('attachment')) {
            $request->validate([
                'attachment' => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
            ]);
            $path = $request->file('attachment')->store('attachments', 'public');
            $examination->update(['attachment_path' => $path]);
            return response()->json($examination);
        }
        
        return response()->json(['error' => 'No attachment uploaded'], 400);
    }
}