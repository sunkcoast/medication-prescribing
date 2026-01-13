<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Prescription;
use App\Models\Payment;
use App\Services\MedicineApiService;
use App\Services\MedicinePricingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class PharmacistController extends Controller
{
    protected $apiService;
    protected $pricingService;

    // Dependency Injection melalui Constructor
    public function __construct(MedicineApiService $apiService, MedicinePricingService $pricingService)
    {
        $this->apiService = $apiService;
        $this->pricingService = $pricingService;
    }

    public function index()
    {
        $prescriptions = Prescription::with(['examination.patient', 'items'])
                            ->latest()
                            ->get();

        return view('pharmacist.index', compact('prescriptions'));
    }

    /**
     * Fitur 3a: Mengambil harga dari API eksternal sesuai tanggal pemeriksaan
     */
    public function calculatePrice($id)
    {
        $prescription = Prescription::with(['items', 'examination'])->findOrFail($id);

        // Pastikan hanya status pending yang bisa dihitung
        if ($prescription->status !== 'pending') {
            return back()->with('error', 'Status resep tidak valid untuk perhitungan harga.');
        }

        try {
            DB::transaction(function () use ($prescription) {
                $totalAmount = 0;

                foreach ($prescription->items as $item) {
                    // 1. Ambil data harga dari API RS Delta Surya
                    $priceData = $this->apiService->getMedicinePrices($item->medicine_id);

                    // 2. Tentukan harga berdasarkan tanggal pemeriksaan (Examination Date)
                    $resolved = $this->pricingService->resolveByDate(
                        $priceData, 
                        $prescription->examination->created_at
                    );

                    // 3. Update item resep dengan harga yang ditemukan
                    $unitPrice = $resolved['unit_price'];
                    $item->update([
                        'price' => $unitPrice, // Asumsi kolom harga di table items adalah 'price'
                        // Tambahan: jika ada kolom subtotal bisa diisi di sini
                    ]);

                    $totalAmount += ($unitPrice * $item->quantity);
                }

                // 4. Update status resep & total amount
                $prescription->update([
                    'status' => 'calculated',
                    'total_amount' => $totalAmount
                ]);
            });

            return back()->with('success', 'Harga obat berhasil diperbarui dari sistem pusat.');

        } catch (Exception $e) {
            Log::error("CALCULATE_PRICE_FAILED: " . $e->getMessage());
            return back()->with('error', 'Gagal mengambil harga: ' . $e->getMessage());
        }
    }

    /**
     * Fitur 4a: Melayani Pembayaran
     */
    public function pay($id)
    {
        $prescription = Prescription::with('items')->findOrFail($id);
    
        if ($prescription->status !== 'calculated') {
            return back()->with('error', 'Resep harus melalui proses hitung harga terlebih dahulu.');
        }
    
        try {
            DB::transaction(function() use ($prescription) {
                // Simpan record pembayaran
                Payment::create([
                    'prescription_id' => $prescription->id,
                    'amount' => $prescription->total_amount ?? $prescription->items->sum(function($i) { 
                        return $i->price * $i->quantity; 
                    }),
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);

                // Update status resep
                $prescription->update(['status' => 'paid']);

            });

            return back()->with('success', 'Pembayaran berhasil dikonfirmasi.');

        } catch (Exception $e) {
            Log::error("PAYMENT_FAILED: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memproses pembayaran.');
        }
    }

    public function printPdf($id)
    {
        $prescription = Prescription::with(['examination.patient', 'examination.doctor', 'items'])->findOrFail($id);
        
        if ($prescription->status !== 'paid') {
            return back()->with('error', 'Resi hanya dapat dicetak untuk resep yang sudah lunas.');
        }

        // Pastikan view pdf.prescription sudah Anda buat
        $pdf = Pdf::loadView('pdf.prescription', compact('prescription'));
        return $pdf->stream("resi-pembayaran-{$prescription->id}.pdf");
    }
}