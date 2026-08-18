<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'tender_id', 'supplier_id', 'reference', 'total_amount',
        'currency', 'compliance_declaration', 'status', 'technical_score',
        'financial_score', 'total_score', 'submitted_at', 'evaluated_by', 'evaluated_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'evaluated_at' => 'datetime',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(BidItem::class);
    }

    public function scores()
    {
        return $this->hasMany(BidScore::class);
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    public function award()
    {
        return $this->hasOne(Award::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'submitted' => 'Submitted',
            'evaluated' => 'Evaluated',
            'awarded'   => 'Awarded',
            'rejected'  => 'Rejected',
            'withdrawn' => 'Withdrawn',
            default     => ucfirst($this->status),
        };
    }
}
