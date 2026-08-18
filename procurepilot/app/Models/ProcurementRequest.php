<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcurementRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'reference', 'title', 'justification', 'department_id',
        'requester_id', 'category_id', 'budget_code', 'required_date', 'priority',
        'estimated_cost', 'currency', 'status', 'approved_by', 'approved_at',
        'rejection_reason', 'notes',
    ];

    protected $casts = [
        'required_date' => 'date',
        'approved_at'   => 'datetime',
        'estimated_cost'=> 'decimal:2',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(ProcurementRequestItem::class);
    }

    public function approvals()
    {
        return $this->morphMany(ApprovalRecord::class, 'approvable');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'draft'     => 'Draft',
            'submitted' => 'Pending Approval',
            'approved'  => 'Approved',
            'rejected'  => 'Rejected',
            default     => ucfirst($this->status),
        };
    }
}
