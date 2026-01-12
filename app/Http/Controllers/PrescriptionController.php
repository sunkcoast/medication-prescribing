<?php

namespace App\Http\Controllers;

use App\Models\Examination;
use App\Models\Prescription;
use App\Services\MedicineApiService;
use App\Services\MedicinePricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PrescriptionController extends Controller
{
    protected $medicineApi;
    protected $pricingService;

    public function __construct(MedicineApiService $api, MedicinePricingService $pricing)
    {
        $this->medicineApi = $api;
        $this->pricingService = $pricing;
    }

    public function store(Request $request)
    {
        $this->authorize('create', Prescription::class);

        $validated = $request->validate([
            'examination_id' => 'required|exists:examinations,id',
        ]);

        $examination = Examination::findOrFail($validated['examination_id']);
        $this->ensurePrescriptionCanBeCreated($examination);

        $prescription = Prescription::create([
            'examination_id' => $examination->id,
            'doctor_id'      => auth()->id(),
            'status'         => 'pending',
            'examined_at'    => $examination->examined_at, 
        ]);

        return response()->json([
            'message' => 'Prescription created successfully',
            'data'    => $prescription,
        ], 201);
    }

    public function addItem(Request $request, Prescription $prescription)
    {
        $request->validate([
            'medicine_id'   => 'required|string',
            'medicine_name' => 'required|string',
            'quantity'      => 'required|integer|min:1',
        ]);

        try {
            $prices = $this->medicineApi->getMedicinePrices($request->medicine_id);

            $validPrice = $this->pricingService->resolveByDate(
                $prices, 
                $prescription->examined_at 
            );

            return DB::transaction(function () use ($request, $prescription, $validPrice) {
                $item = $prescription->items()
                    ->where('medicine_id', $request->medicine_id)
                    ->first();

                if ($item) {
                    $item->increment('quantity', $request->quantity);

                    $item->update(['total_price' => $item->quantity * $item->unit_price]);
                } else {
                    $prescription->items()->create([
                        'medicine_id'   => $request->medicine_id,
                        'medicine_name' => $request->medicine_name,
                        'quantity'      => $request->quantity,
                        'unit_price'    => $validPrice['unit_price'],
                        'total_price'   => $validPrice['unit_price'] * $request->quantity,
                    ]);
                }

                return response()->json(['message' => 'Item berhasil ditambahkan']);
            });

        } catch (RuntimeException $e) {
            return response()->json([
                'message' => 'Gagal mendapatkan harga: ' . $e->getMessage()
            ], 422);
        }
    }
}