<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PharmacistController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PrescriptionPdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // SEMUA FITUR DOKTER
    Route::middleware('role:doctor')->prefix('doctor')->name('doctor.')->group(function () {

        Route::post('/examinations', [DoctorController::class, 'storeExamination']);
        Route::post('/examinations/{id}/attachment', [DoctorController::class, 'uploadAttachment']);

        Route::post('/prescriptions', [PrescriptionController::class, 'store']);
        Route::post('/prescriptions/{prescription}/items', [PrescriptionController::class, 'addItem']);
    });

    // SEMUA FITUR APOTEKER
    Route::middleware('role:pharmacist')->prefix('pharmacist')->name('pharmacist.')->group(function () {
        Route::get('/prescriptions', [PharmacistController::class, 'index']);
        Route::post('/prescriptions/{id}/calculate', [PharmacistController::class, 'calculatePrice']);
        Route::post('/prescriptions/{id}/pay', [PharmacistController::class, 'pay']);
        Route::get('/prescriptions/{id}/print', [PharmacistController::class, 'printPdf']);
    });
});

require __DIR__.'/auth.php';
