<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'role', 'module',
        'can_view', 'can_create', 'can_edit', 'can_delete', 'can_approve',
    ];

    protected $casts = [
        'can_view'   => 'boolean',
        'can_create' => 'boolean',
        'can_edit'   => 'boolean',
        'can_delete' => 'boolean',
        'can_approve'=> 'boolean',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Canonical list of modules shown in the sidebar.
     */
    public static function modules(): array
    {
        return [
            'dashboard'      => 'Dashboard',
            'plans'          => 'Procurement Plans',
            'tenders'        => 'Tenders',
            'awards'         => 'Awards',
            'contracts'      => 'Contracts',
            'purchase-orders'=> 'Purchase Orders',
            'requests'       => 'Procurement Requests',
            'invoices'       => 'Supplier Invoices',
            'budgets'        => 'Budgets',
            'suppliers'      => 'Suppliers',
            'categories'     => 'Categories',
            'reports'        => 'Reports',
            'workflow'       => 'Approval Workflow',
            'compliance'     => 'Compliance',
            'audit'          => 'Audit Trail',
            'users'          => 'Users',
            'billing'        => 'Billing',
            'settings'       => 'Settings',
        ];
    }

    /**
     * Default grants per role. superadmin/admin always bypass checks.
     * 'v','c','e','d','a' map to view/create/edit/delete/approve.
     */
    public static function defaultGrants(): array
    {
        return [
            'procurement' => [
                'dashboard'       => 'v',
                'plans'           => 'vce',
                'tenders'         => 'vce',
                'awards'          => 'v',
                'contracts'       => 'vce',
                'purchase-orders' => 'vce',
                'requests'        => 'vce',
                'invoices'        => 'vce',
                'budgets'         => 'vce',
                'suppliers'       => 'vce',
                'categories'      => 'vce',
                'reports'         => 'v',
                'workflow'        => 'v',
                'compliance'      => 'v',
            ],
            'approver' => [
                'dashboard' => 'v',
                'plans'     => 'va',
                'tenders'   => 'va',
                'awards'    => 'va',
                'contracts' => 'va',
                'requests'  => 'va',
                'purchase-orders' => 'va',
                'invoices'  => 'va',
                'suppliers' => 'va',
                'reports'   => 'v',
                'compliance'=> 'v',
            ],
            'auditor' => [
                'dashboard' => 'v',
                'tenders'   => 'v',
                'awards'    => 'v',
                'contracts' => 'v',
                'purchase-orders' => 'v',
                'requests'  => 'v',
                'invoices'  => 'v',
                'budgets'   => 'v',
                'suppliers' => 'v',
                'reports'   => 'v',
                'compliance'=> 'v',
                'audit'     => 'v',
            ],
            'staff' => [
                'dashboard' => 'v',
                'tenders'   => 'v',
                'requests'  => 'vc',
                'categories'=> 'v',
            ],
            'supplier' => [
                'dashboard' => 'v',
                'tenders'   => 'v',
                'bids'      => 'vc',
            ],
        ];
    }
}
