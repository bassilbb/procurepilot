<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'organization_id', 'user_id', 'action', 'auditable_type',
        'auditable_id', 'ip_address', 'before', 'after',
    ];

    protected $casts = [
        'before' => 'array',
        'after'  => 'array',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function record(string $action, ?Model $auditable = null, array $before = [], array $after = [], $user = null, $org = null)
    {
        return self::create([
            'organization_id' => $org?->id ?? optional($user?->organization ?? auth()->user()?->organization)->id,
            'user_id'         => $user?->id ?? auth()->id(),
            'action'          => $action,
            'auditable_type'  => $auditable ? $auditable::class : null,
            'auditable_id'    => $auditable?->getKey(),
            'ip_address'      => request()->ip(),
            'before'          => $before,
            'after'           => $after,
        ]);
    }
}
