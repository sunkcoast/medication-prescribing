<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Prescription extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'examination_id',
        'doctor_id',
        'pharmacist_id',
        'examined_at',
        'status',
    ];

    protected $casts = [
        'examined_at' => 'datetime',
    ];

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function examination()
    {
        return $this->belongsTo(Examination::class);
    }

    public function pharmacist()
    {
        return $this->belongsTo(User::class, 'pharmacist_id');
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

    public function isServed(): bool
    {
        return $this->status === 'served';
    }

    public function items()
    {
        return $this->hasMany(PrescriptionItem::class);
    }
}
