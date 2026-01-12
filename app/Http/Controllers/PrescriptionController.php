<?php

namespace App\Http\Controllers;

use RuntimeException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Examination;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Services\MedicineApiService;
use App\Services\MedicinePricingService;

class PrescriptionController extends Controller
{
    use AuthorizesRequests;
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
        

        $prescription = Prescription::create([
            'examination_id' => $examination->id,
            'doctor_id'      => auth()->id(),
            'status'         => 'pending',
            'examined_at'    => $examination->examined_at, 
        ]);

        return redirect()->route('doctor.prescriptions.edit', $prescription->id)
                        ->with('success', 'Prescription created. Please add medicines.');
    }

    public function edit($id)
    {
        $prescription = Prescription::with(['examination.patient', 'items'])->findOrFail($id);
        
        try {
            $medicines = $this->medicineApi->getMedicines(); 

            return view('doctor.prescriptions.edit', compact('prescription', 'medicines'));
        } catch (\Exception $e) {
            return view('doctor.prescriptions.edit', compact('prescription'))
                ->withErrors(['api_error' => 'Could not fetch medicines from API: ' . $e->getMessage()]);
        }
    }

    public function addItem(Request $request, $prescriptionId, MedicineApiService $apiService, MedicinePricingService $pricingService)
    {
        $request->validate([
            'medicine_id' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $prescription = Prescription::with('examination')->findOrFail($prescriptionId);

        try {
            // 1. Ambil semua history harga untuk obat tersebut dari API
            $prices = $apiService->getMedicinePrices($request->medicine_id);
            
            // 2. Cari harga yang valid berdasarkan tanggal pemeriksaan (examined_at)
            $examDate = \Carbon\Carbon::parse($prescription->examination->examined_at);
            $resolvedPrice = $pricingService->resolveByDate($prices, $examDate);

            // Validasi: Jika harga tidak ditemukan atau 0
            if (!$resolvedPrice || $resolvedPrice['unit_price'] <= 0) {
                throw new \Exception('Price not found for this medicine on the examination date.');
            }

            // 3. Ambil Nama Obat
            $medicines = $apiService->getMedicines();
            $medicineData = collect($medicines)->firstWhere('id', $request->medicine_id);
            $medicineName = $medicineData['name'] ?? 'Unknown Medicine';

            // 4. Simpan ke database menggunakan unit_price
            // Pastikan kolom-kolom ini ada di migration prescription_items
            $prescription->items()->create([
                'medicine_id'      => $request->medicine_id,
                'medicine_name'    => $medicineName,
                'unit_price'       => $resolvedPrice['unit_price'], // Harga satuan
                'quantity'         => $request->quantity,
                'total_price'      => $resolvedPrice['unit_price'] * $request->quantity, // Total bill
                'price_start_date' => $resolvedPrice['price_start_date'] ?? null,
                'price_end_date'   => $resolvedPrice['price_end_date'] ?? null,
            ]);

            return redirect()->back()->with('success', "Added $medicineName (Rp " . number_format($resolvedPrice['unit_price']) . ") to prescription.");

        } catch (\Exception $e) {
            Log::error("Pricing Error for ID {$request->medicine_id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Pricing Error: ' . $e->getMessage());
        }
    }

    public function removeItem($Id)
    {
        $item = \App\Models\PrescriptionItem::findOrFail($Id);
        $prescription = $item->prescription;

        // Proteksi: Jangan biarkan hapus jika sudah diproses apoteker
        if ($prescription->status !== 'pending') {
            return redirect()->back()->with('error', 'Cannot modify prescription. It is already being processed.');
        }

        $item->delete();
        return redirect()->back()->with('success', 'Medicine removed from prescription.');
    }

    public function finish($id)
    {
        $prescription = Prescription::findOrFail($id);
        
        // Anda bisa mengubah status jika diperlukan, misalnya 'submitted' 
        // agar apoteker tahu ini sudah final.
        $prescription->update(['status' => 'pending']); 

        return redirect()->route('doctor.examinations')
                        ->with('success', 'Prescription for ' . $prescription->examination->patient->name . ' has been sent to pharmacy.');
    }

    private function ensurePrescriptionCanBeCreated(Examination $examination)
    {
        if ($examination->prescription) {
            throw new \RuntimeException('A prescription already exists for this examination.');
        }
    }
}