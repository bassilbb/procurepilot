<?php

namespace App\Http\Controllers;

use App\Models\ApprovalLevel;
use App\Models\ApprovalRecord;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;

class WorkflowController extends Controller
{
    public function index()
    {
        $org = currentOrganization();

        $departments = Department::where('organization_id', $org->id)
            ->with('manager', 'users')
            ->orderBy('name')
            ->get();

        $levels = ApprovalLevel::where('organization_id', $org->id)
            ->orderBy('sequence')
            ->get();

        $pending = ApprovalRecord::where('organization_id', $org->id)
            ->where('status', 'pending')
            ->with(['level', 'approvable', 'approver'])
            ->latest()
            ->take(10)
            ->get();

        $records = ApprovalRecord::where('organization_id', $org->id)
            ->with(['level', 'approver', 'approvable'])
            ->latest()
            ->paginate(10);

        $users = User::where('organization_id', $org->id)->orderBy('name')->get();

        return view('workflow.index', compact('departments', 'levels', 'pending', 'records', 'users'));
    }

    public function storeDepartment(Request $request)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'code'        => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
            'manager_id'  => ['nullable', 'exists:users,id'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $data['organization_id'] = currentOrganization()->id;
        $data['is_active'] = $request->boolean('is_active', true);

        $department = Department::create($data);

        AuditLog::record('department_created', $department, [], $department->toArray());

        return back()->with('success', 'Department created.');
    }

    public function updateDepartment(Request $request, Department $department)
    {
        abort_unless($department->organization_id === currentOrganization()->id, 403);
        abort_unless(auth()->user()->isAdmin(), 403);

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'code'        => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
            'manager_id'  => ['nullable', 'exists:users,id'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $before = $department->toArray();
        $department->update($data);

        AuditLog::record('department_updated', $department, $before, $department->toArray());

        return back()->with('success', 'Department updated.');
    }

    public function destroyDepartment(Department $department)
    {
        abort_unless($department->organization_id === currentOrganization()->id, 403);
        abort_unless(auth()->user()->isAdmin(), 403);
        abort_unless($department->users()->count() === 0, 403);

        AuditLog::record('department_deleted', $department, $department->toArray(), []);
        $department->delete();

        return back()->with('success', 'Department removed.');
    }

    public function storeLevel(Request $request)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'sequence'   => ['required', 'integer', 'min:1'],
            'role'       => ['required', 'string', 'in:admin,approver,procurement,auditor'],
            'min_amount' => ['required', 'numeric', 'min:0'],
            'max_amount' => ['nullable', 'numeric', 'gt:min_amount'],
            'is_active'  => ['nullable', 'boolean'],
        ]);

        $data['organization_id'] = currentOrganization()->id;
        $data['is_active'] = $request->boolean('is_active', true);

        $level = ApprovalLevel::create($data);

        AuditLog::record('approval_level_created', $level, [], $level->toArray());

        return back()->with('success', 'Approval level created.');
    }

    public function updateLevel(Request $request, ApprovalLevel $level)
    {
        abort_unless($level->organization_id === currentOrganization()->id, 403);
        abort_unless(auth()->user()->isAdmin(), 403);

        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'sequence'   => ['required', 'integer', 'min:1'],
            'role'       => ['required', 'string', 'in:admin,approver,procurement,auditor'],
            'min_amount' => ['required', 'numeric', 'min:0'],
            'max_amount' => ['nullable', 'numeric', 'gt:min_amount'],
            'is_active'  => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $before = $level->toArray();
        $level->update($data);

        AuditLog::record('approval_level_updated', $level, $before, $level->toArray());

        return back()->with('success', 'Approval level updated.');
    }

    public function destroyLevel(ApprovalLevel $level)
    {
        abort_unless($level->organization_id === currentOrganization()->id, 403);
        abort_unless(auth()->user()->isAdmin(), 403);
        abort_unless($level->records()->count() === 0, 403);

        AuditLog::record('approval_level_deleted', $level, $level->toArray(), []);
        $level->delete();

        return back()->with('success', 'Approval level removed.');
    }
}
