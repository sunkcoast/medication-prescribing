<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Examination;
use App\Models\Patient;

class DoctorController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $patients = Patient::whereDoesntHave('examinations')->get();

        $examinations = Examination::with(['patient', 'prescription']) 
            ->where('doctor_id', auth()->id())
            ->latest()
            ->get();

        return view('doctor.index', compact('examinations', 'patients'));
    }

    // Fitur 3a: Menyimpan Pemeriksaan
    public function storeExamination(Request $request)
    {
        $this->authorize('create', Examination::class);

        $request->validate([
        'patient_id'       => 'required|exists:patients,id',
        'examined_at'      => 'required|date',
        'height'           => 'nullable|numeric',
        'weight'           => 'nullable|numeric',
        'systole'          => 'nullable|integer',
        'diastole'         => 'nullable|integer',
        'heart_rate'       => 'nullable|integer',
        'respiration_rate' => 'nullable|integer',
        'temperature'      => 'nullable|numeric',
        'notes'            => 'nullable|string',
        ]);

        $examination = Examination::create([
            'doctor_id'        => auth()->id(),
            'patient_id'       => $request->patient_id,
            'examined_at'      => $request->examined_at,
            'height'           => $request->height,
            'weight'           => $request->weight,
            'systole'          => $request->systole,
            'diastole'         => $request->diastole,
            'heart_rate'       => $request->heart_rate,
            'respiration_rate' => $request->respiration_rate,
            'temperature'      => $request->temperature,
            'notes'            => $request->notes,
            'status'           => 'completed',
        ]);

        return redirect()->back()->with('success', 'Examination record saved successfully.');
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
        
        return redirect()->back()->with('success', 'Attachment uploaded successfully!');
    }
}