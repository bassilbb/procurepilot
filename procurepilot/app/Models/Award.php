<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Award extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'tender_id', 'bid_id', 'supplier_id', 'award_amount',
        'currency', 'justification', 'status', 'decided_by', 'decided_at',
    ];

    protected $casts = ['decided_at' => 'datetime'];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function bid()
    {
        return $this->belongsTo(Bid::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function decisionMaker()
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }
}
