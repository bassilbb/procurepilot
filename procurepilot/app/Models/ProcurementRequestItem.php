<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcurementRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'procurement_request_id', 'description', 'quantity', 'unit',
        'estimated_unit_cost', 'estimated_total',
    ];

    protected $casts = [
        'quantity'           => 'decimal:2',
        'estimated_unit_cost'=> 'decimal:2',
        'estimated_total'    => 'decimal:2',
    ];

    public function request()
    {
        return $this->belongsTo(ProcurementRequest::class, 'procurement_request_id');
    }
}
