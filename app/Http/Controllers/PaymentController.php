<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prescription;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function pay(Request $request, $prescriptionId)
    {
        $prescription = Prescription::findOrFail($prescriptionId);
    
        $this->authorize('pay', $prescription); 
    
        if (! $prescription->isCalculated()) {
            return response()->json([
                'error' => 'Prescription must be calculated before payment'
            ], 400);
        }
    
        $request->validate([
            'amount' => 'numeric|min:0'
        ]);
    
        $payment = Payment::create([
            'prescription_id' => $prescription->id,
            'amount' => $request->amount ?? $prescription->total_price,
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    
        $prescription->status = 'paid';
        $prescription->save();
    
        return response()->json([
            'message' => 'Payment successful',
            'payment' => $payment,
            'prescription' => $prescription
        ]);
    }
    
}
