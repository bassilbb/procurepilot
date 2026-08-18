<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tender extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'reference', 'title', 'description', 'category_id',
        'type', 'method', 'budget', 'currency', 'published_at', 'closing_at',
        'opening_at', 'evaluation_method', 'status', 'created_by',
        'approved_by', 'approved_at', 'award_notice',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'closing_at'   => 'datetime',
        'opening_at'   => 'datetime',
        'approved_at'  => 'datetime',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function items()
    {
        return $this->hasMany(TenderItem::class);
    }

    public function criteria()
    {
        return $this->hasMany(EvaluationCriterion::class);
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'tender_suppliers')
            ->withTimestamps();
    }

    public function bids()
    {
        return $this->hasMany(Bid::class);
    }

    public function awards()
    {
        return $this->hasMany(Award::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isOpen(): bool
    {
        return $this->status === 'published'
            && $this->closing_at
            && $this->closing_at->isFuture();
    }

    public function estimatedBudget(): float
    {
        return (float) $this->items()->sum(\Illuminate\Support\Facades\DB::raw('quantity * COALESCE(estimated_unit_price, 0)'));
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'draft'           => 'Draft',
            'published'       => 'Published',
            'closed'          => 'Closed',
            'under_evaluation'=> 'Under Evaluation',
            'awarded'         => 'Awarded',
            'cancelled'       => 'Cancelled',
            default           => ucfirst($this->status),
        };
    }

    public function typeLabel(): string
    {
        return $this->type === 'open' ? 'Open' : 'Restricted';
    }

    public function methodLabel(): string
    {
        return match ($this->method) {
            'open_competitive' => 'Open Competitive Bidding',
            'restricted'       => 'Restricted Bidding',
            'direct'           => 'Direct Procurement',
            default            => ucwords(str_replace('_', ' ', $this->method)),
        };
    }
}
