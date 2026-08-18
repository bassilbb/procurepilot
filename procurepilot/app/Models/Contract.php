<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'reference', 'title', 'description', 'supplier_id',
        'tender_id', 'award_id', 'value', 'currency', 'start_date', 'end_date',
        'payment_terms', 'status', 'created_by', 'signed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'signed_at'  => 'datetime',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function award()
    {
        return $this->belongsTo(Award::class);
    }

    public function milestones()
    {
        return $this->hasMany(ContractMilestone::class);
    }

    public function documents()
    {
        return $this->hasMany(ContractDocument::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'draft'      => 'Draft',
            'active'     => 'Active',
            'completed'  => 'Completed',
            'terminated' => 'Terminated',
            'expired'    => 'Expired',
            default      => ucfirst($this->status),
        };
    }

    public function isExpiringSoon(): bool
    {
        return $this->status === 'active'
            && $this->end_date
            && $this->end_date->isAfter(now())
            && $this->end_date->diffInDays(now()) <= 30;
    }
}
