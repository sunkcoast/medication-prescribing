<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'examination_id',
        'pharmacist_id',
        'medicines',
        'status',
    ];

    protected $casts = [
        'medicines' => 'array',
    ];

    public function examination()
    {
        return $this->belongsTo(Examination::class);
    }

    public function pharmacist()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCalculated(): bool
    {
        return $this->status === 'calculated';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isLocked(): bool
    {
        return $this->status === 'locked';
    }
}
