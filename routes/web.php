<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PharmacistController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ActivityLogController;
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
});

Route::middleware('auth')->group(function () {
    
    Route::middleware('role:doctor')->prefix('doctor')->name('doctor.')->group(function () {

        Route::get('/examinations', [DoctorController::class, 'index'])->name('examinations'); 
        Route::post('/examinations', [DoctorController::class, 'storeExamination'])->name('examinations.store');
        Route::post('/examinations/{id}/attachment', [DoctorController::class, 'uploadAttachment'])->name('examinations.attachment');

        Route::post('/prescriptions', [PrescriptionController::class, 'store'])->name('prescriptions.store');
        Route::post('/prescriptions/{id}/items', [PrescriptionController::class, 'addItem'])->name('prescriptions.items.add');
        Route::delete('/prescriptions/items/{id}', [PrescriptionController::class, 'removeItem'])->name('prescriptions.items.remove');
        Route::get('/prescriptions/{id}/edit', [PrescriptionController::class, 'edit'])->name('prescriptions.edit');
        Route::post('/prescriptions/{id}/finish', [PrescriptionController::class, 'finish'])->name('prescriptions.finish');
    
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('logs.index');
    });

    Route::middleware('role:pharmacist')->prefix('pharmacist')->name('pharmacist.')->group(function () {
        Route::get('/prescriptions', [PharmacistController::class, 'index'])->name('prescriptions.index');
        Route::post('/prescriptions/{id}/calculate', [PharmacistController::class, 'calculatePrice'])->name('prescriptions.calculate');
        Route::post('/prescriptions/{id}/pay', [PharmacistController::class, 'pay'])->name('prescriptions.pay');
        Route::get('/prescriptions/{id}/print', [PharmacistController::class, 'printPdf'])->name('prescriptions.print');
    });
});

require __DIR__.'/auth.php';