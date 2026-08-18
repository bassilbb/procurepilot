<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierInvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_invoice_id', 'po_item_id', 'description', 'quantity',
        'unit', 'unit_price', 'total_price',
    ];

    protected $casts = [
        'quantity'    => 'decimal:2',
        'unit_price'  => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(SupplierInvoice::class, 'supplier_invoice_id');
    }

    public function poItem()
    {
        return $this->belongsTo(PoItem::class);
    }
}
