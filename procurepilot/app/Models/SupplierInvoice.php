<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'number', 'supplier_id', 'purchase_order_id', 'contract_id',
        'invoice_date', 'due_date', 'subtotal', 'tax_amount', 'total', 'currency',
        'status', 'match_status', 'match_detail', 'notes', 'verified_by', 'verified_at',
        'approved_by', 'approved_at', 'paid_at',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date'     => 'date',
        'subtotal'     => 'decimal:2',
        'tax_amount'   => 'decimal:2',
        'total'        => 'decimal:2',
        'match_detail' => 'array',
        'verified_at'  => 'datetime',
        'approved_at'  => 'datetime',
        'paid_at'      => 'datetime',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function items()
    {
        return $this->hasMany(SupplierInvoiceItem::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending'  => 'Pending',
            'verified' => 'Verified',
            'matched'  => 'Matched',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'paid'     => 'Paid',
            default    => ucfirst($this->status),
        };
    }

    public function matchStatusLabel(): string
    {
        return match ($this->match_status) {
            'full'      => '3-Way Match Complete',
            'partial'   => 'Partial Match',
            'unmatched' => 'No Match',
            default     => 'Not Checked',
        };
    }
}
