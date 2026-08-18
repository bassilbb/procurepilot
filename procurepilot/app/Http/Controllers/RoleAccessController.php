<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\RolePermission;
use Illuminate\Http\Request;

class RoleAccessController extends Controller
{
    public function index()
    {
        $org = currentOrganization();

        $modules = RolePermission::modules();
        $roles = ['procurement', 'approver', 'auditor', 'staff', 'supplier'];

        $permissions = RolePermission::where('organization_id', $org->id)->get()
            ->keyBy(fn ($p) => $p->role . '.' . $p->module);

        return view('access-control.index', compact('modules', 'roles', 'permissions'));
    }

    public function update(Request $request)
    {
        $org = currentOrganization();

        $modules = array_keys(RolePermission::modules());
        $roles = ['procurement', 'approver', 'auditor', 'staff', 'supplier'];

        $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*.*' => ['nullable', 'array'],
        ]);

        $data = $request->input('permissions', []);
        $saved = 0;

        foreach ($roles as $role) {
            foreach ($modules as $module) {
                $value = $data[$role][$module] ?? [];
                $value = array_values($value);

                RolePermission::updateOrCreate(
                    ['organization_id' => $org->id, 'role' => $role, 'module' => $module],
                    [
                        'can_view'    => in_array('view', $value),
                        'can_create'  => in_array('create', $value),
                        'can_edit'    => in_array('edit', $value),
                        'can_delete'  => in_array('delete', $value),
                        'can_approve' => in_array('approve', $value),
                    ]
                );

                $saved++;
            }
        }

        AuditLog::record('role_permissions_updated', $org, [], ['roles' => $roles, 'modules' => $modules]);

        return back()->with('success', 'Role access updated (' . $saved . ' module entries saved).');
    }
}
