<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'invoice_id', 'amount', 'currency', 'method',
        'gateway', 'reference', 'status', 'paid_at', 'metadata',
    ];

    protected $casts = [
        'paid_at'  => 'datetime',
        'metadata' => 'array',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
