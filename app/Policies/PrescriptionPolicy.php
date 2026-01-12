<?php

namespace App\Policies;

use App\Models\Prescription;
use App\Models\User;

class PrescriptionPolicy
{
    /**
     * Determine whether the user can view any models.
     * (Misal list semua resep, kita buat false untuk saat ini)
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view a prescription.
     * Hanya Pharmacist bisa lihat resep
     */
    public function view(User $user, Prescription $prescription): bool
    {
        return $user->role === 'pharmacist';
    }

    /**
     * Determine whether the user can create a prescription.
     * Hanya Doctor bisa create dari pemeriksaan
     */
    public function create(User $user): bool
    {
        return $user->role === 'doctor';
    }

    /**
     * Determine whether the user can update a prescription.
     * Tidak boleh update manual sekarang
     */
    public function update(User $user, Prescription $prescription): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete a prescription.
     * Tidak diperbolehkan
     */
    public function delete(User $user, Prescription $prescription): bool
    {
        return false;
    }

    /**
     * Determine whether the user can calculate price.
     * Pharmacist boleh calculate jika status pending
     */
    public function calculate(User $user, Prescription $prescription): bool
    {
        return $user->role === 'pharmacist' && $prescription->status === 'pending';
    }

    /**
     * Determine whether the user can lock a prescription.
     * Pharmacist boleh lock hanya jika status sudah paid
     */
    public function lock(User $user, Prescription $prescription): bool
    {
        return $user->role === 'pharmacist' && $prescription->status === 'paid';
    }

    /**
     * Restore & forceDelete tetap false
     */
    public function restore(User $user, Prescription $prescription): bool
    {
        return false;
    }

    public function forceDelete(User $user, Prescription $prescription): bool
    {
        return false;
    }
}
