<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'organization_id', 'department_id', 'role',
        'title', 'phone', 'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'superadmin']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isProcurement(): bool
    {
        return in_array($this->role, ['admin', 'procurement']);
    }

    public function isApprover(): bool
    {
        return in_array($this->role, ['admin', 'approver']);
    }

    public function isAuditor(): bool
    {
        return in_array($this->role, ['admin', 'auditor']);
    }

    public function isSupplier(): bool
    {
        return $this->role === 'supplier';
    }

    /**
     * Full-access roles (superadmin + admin) always bypass permission checks.
     */
    public function isFullAccess(): bool
    {
        return in_array($this->role, ['superadmin', 'admin']);
    }

    /**
     * Determine whether the user may access a module/action.
     * Module keys map to RolePermission::modules().
     */
    public function canAccess(string $module, string $action = 'view'): bool
    {
        if ($this->isFullAccess()) {
            return true;
        }

        $permission = RolePermission::where('organization_id', $this->organization_id)
            ->where('role', $this->role)
            ->where('module', $module)
            ->first();

        if (! $permission) {
            return false;
        }

        return match ($action) {
            'create' => $permission->can_create,
            'edit'   => $permission->can_edit,
            'delete' => $permission->can_delete,
            'approve'=> $permission->can_approve,
            default  => $permission->can_view,
        };
    }

    /**
     * Modules this role can at least view (used to filter the sidebar).
     */
    public function permittedModules(): array
    {
        if ($this->isFullAccess()) {
            return array_keys(RolePermission::modules());
        }

        return RolePermission::where('organization_id', $this->organization_id)
            ->where('role', $this->role)
            ->where('can_view', true)
            ->pluck('module')
            ->all();
    }

    public static function roleOptions(): array
    {
        return [
            'superadmin'  => 'Super Admin',
            'admin'       => 'Administrator',
            'procurement' => 'Procurement Officer',
            'approver'    => 'Tenders Board / Approver',
            'auditor'     => 'Auditor / Compliance',
            'supplier'    => 'Supplier',
            'staff'       => 'Staff',
        ];
    }
}
