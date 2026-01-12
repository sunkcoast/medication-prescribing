<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prescription;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PharmacistController extends Controller
{
    // Fitur 4a: Melihat Resep
    public function index()
    {
        return Prescription::with(['examination.patient', 'items'])->latest()->get();
    }

    // Proses internal: Hitung harga
    public function calculatePrice($id)
    {
        $prescription = Prescription::with('items')->findOrFail($id);
        $this->authorize('calculate', $prescription);

        if (!$prescription->isPending()) {
            return response()->json(['error' => 'Status must be pending'], 400);
        }

        $prescription->update(['status' => 'calculated']);
        return response()->json(['message' => 'Price calculated', 'total' => $prescription->items->sum('total_price')]);
    }

    // Fitur 4a: Melayani Pembayaran
    public function pay(Request $request, $id)
    {
        $prescription = Prescription::findOrFail($id);
        $this->authorize('pay', $prescription); 
    
        if (!$prescription->isCalculated()) {
            return response()->json(['error' => 'Prescription must be calculated'], 400);
        }
    
        return DB::transaction(function() use ($request, $prescription) {
            $payment = Payment::create([
                'prescription_id' => $prescription->id,
                'amount' => $request->amount ?? $prescription->items->sum('total_price'),
                'status' => 'paid',
                'paid_at' => now(),
            ]);
            $prescription->update(['status' => 'paid']);
            return response()->json(['message' => 'Payment successful', 'payment' => $payment]);
        });
    }

    // Fitur 4b: Cetak PDF
    public function printPdf($id)
    {
        $prescription = Prescription::with(['examination.patient', 'doctor', 'items'])->findOrFail($id);
        
        // Opsional: Resi hanya bisa dicetak jika sudah lunas
        if (!$prescription->isPaid()) {
            return response()->json(['error' => 'Payment required'], 403);
        }

        $pdf = Pdf::loadView('pdf.prescription', ['prescription' => $prescription]);
        return $pdf->stream("resi-{$id}.pdf");
    }
}