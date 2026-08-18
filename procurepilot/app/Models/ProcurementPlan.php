<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcurementPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'title', 'fiscal_year', 'description', 'status',
        'created_by', 'approved_by', 'approved_at',
    ];

    protected $casts = ['approved_at' => 'datetime'];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function items()
    {
        return $this->hasMany(ProcurementPlanItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function totalEstimatedCost(): float
    {
        return (float) $this->items()->sum('estimated_cost');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'draft'    => 'Draft',
            'submitted'=> 'Awaiting Approval',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default    => ucfirst($this->status),
        };
    }
}
