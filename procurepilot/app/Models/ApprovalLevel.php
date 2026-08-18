<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'name', 'sequence', 'role',
        'min_amount', 'max_amount', 'is_active',
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'is_active'  => 'boolean',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function records()
    {
        return $this->hasMany(ApprovalRecord::class);
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            'admin'       => 'Administrator',
            'approver'    => 'Approver',
            'procurement' => 'Procurement Officer',
            'auditor'     => 'Auditor / Compliance',
            default       => ucfirst($this->role),
        };
    }

    /**
     * The system roles that are permitted to act on this level.
     */
    public function permittedRoles(): array
    {
        return match ($this->role) {
            'admin'       => ['admin'],
            'approver'    => ['admin', 'approver'],
            'procurement' => ['admin', 'procurement'],
            'auditor'     => ['admin', 'auditor'],
            default       => ['admin'],
        };
    }

    /**
     * Users in the organization that can approve this level.
     */
    public function approverUsers()
    {
        return User::where('organization_id', $this->organization_id)
            ->where('is_active', true)
            ->whereIn('role', $this->permittedRoles())
            ->orderBy('name')
            ->get();
    }
}
