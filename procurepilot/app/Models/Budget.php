<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'name', 'fiscal_year', 'department_id', 'category',
        'allocated_amount', 'committed_amount', 'spent_amount', 'currency',
        'status', 'notes',
    ];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
        'committed_amount' => 'decimal:2',
        'spent_amount'     => 'decimal:2',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function getRemainingAttribute(): float
    {
        return (float) $this->allocated_amount - (float) $this->committed_amount - (float) $this->spent_amount;
    }

    public function getUtilizationPercentAttribute(): float
    {
        if ((float) $this->allocated_amount <= 0) {
            return 0;
        }

        return round((((float) $this->committed_amount + (float) $this->spent_amount) / (float) $this->allocated_amount) * 100, 1);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'draft'   => 'Draft',
            'active'  => 'Active',
            'closed'  => 'Closed',
            default   => ucfirst($this->status),
        };
    }
}
