<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Traits\LogsActivity;

class Examination extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'doctor_id',
        'patient_id',
        'status',
        'examined_at',
        'height',
        'weight',
        'systole',
        'diastole',
        'heart_rate',
        'respiration_rate',
        'temperature',
        'notes',
        'attachment',
    ];

    protected $casts = [
        'examined_at' => 'datetime',
    ];

    public function doctor()
    {
        return $this->belongsTo(User::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function prescription(): HasOne
    {
        return $this->hasOne(Prescription::class);
    }
}
