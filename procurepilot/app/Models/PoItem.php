<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id', 'description', 'quantity', 'unit', 'unit_price',
        'total_price', 'received_qty',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function remaining(): float
    {
        return (float) ($this->quantity - $this->received_qty);
    }
}
