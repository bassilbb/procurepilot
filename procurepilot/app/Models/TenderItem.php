<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenderItem extends Model
{
    use HasFactory;

    protected $fillable = ['tender_id', 'description', 'quantity', 'unit', 'estimated_unit_price'];

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function getTotalEstimateAttribute(): float
    {
        return (float) ($this->quantity * $this->estimated_unit_price);
    }
}
