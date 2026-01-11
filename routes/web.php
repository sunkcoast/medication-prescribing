<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ExaminationController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\PharmacistPrescriptionController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('role:doctor')->group(function () {
        Route::post('/examinations', [ExaminationController::class, 'store']);
        Route::post('/examinations/{id}/attachment', [ExaminationController::class, 'uploadAttachment']);

        Route::get('/prescriptions/create/{examination}', [PrescriptionController::class, 'createFromExamination']);
        Route::post('/prescriptions', [PrescriptionController::class, 'store']);
    });

    Route::middleware('role:pharmacist')->prefix('pharmacist')->group(function () {
        Route::get('/prescriptions', [PharmacistPrescriptionController::class, 'index']);
        Route::post('/prescriptions/{id}/calculate', [PharmacistPrescriptionController::class, 'calculatePrice']);
        Route::post('/prescriptions/{id}/pay', [PaymentController::class, 'pay']);
        Route::post('/prescriptions/{id}/lock', [PharmacistPrescriptionController::class, 'lock']);
    });
    
});

require __DIR__.'/auth.php';
