<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenderSupplier extends Model
{
    use HasFactory;

    protected $fillable = ['tender_id', 'supplier_id', 'invited_at', 'responded_at'];

    protected $casts = [
        'invited_at'  => 'datetime',
        'responded_at'=> 'datetime',
    ];

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
