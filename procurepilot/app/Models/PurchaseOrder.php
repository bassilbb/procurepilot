<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'reference', 'title', 'description', 'supplier_id',
        'contract_id', 'tender_id', 'order_date', 'expected_delivery', 'total',
        'currency', 'status', 'approved_by', 'approved_at',
    ];

    protected $casts = [
        'order_date'       => 'date',
        'expected_delivery'=> 'date',
        'approved_at'      => 'datetime',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function items()
    {
        return $this->hasMany(PoItem::class);
    }

    public function receipts()
    {
        return $this->hasMany(GoodsReceipt::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'draft'             => 'Draft',
            'issued'            => 'Issued',
            'approved'          => 'Approved',
            'partially_received'=> 'Partially Received',
            'received'          => 'Received',
            'cancelled'         => 'Cancelled',
            default             => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function isReceived(): bool
    {
        return in_array($this->status, ['received', 'partially_received']);
    }
}
