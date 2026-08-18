<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'provider', 'public_key', 'secret_key', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public const PROVIDERS = ['paystack', 'flutterwave', 'mono'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function isConfigured(): bool
    {
        return $this->public_key !== null || $this->secret_key !== null;
    }
}
