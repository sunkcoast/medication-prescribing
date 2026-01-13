<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class PrescriptionItem extends Model
{
    use HasFactory;
    use LogsActivity;
    
    protected $fillable = [
        'prescription_id',
        'medicine_id',
        'medicine_name',
        'unit_price',
        'quantity',
        'total_price',
        'price_start_date',
        'price_end_date',
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }
}

