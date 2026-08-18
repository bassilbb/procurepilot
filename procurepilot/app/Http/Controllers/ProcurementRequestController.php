<?php

namespace App\Http\Controllers;

use App\Models\ApprovalLevel;
use App\Models\ApprovalRecord;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Department;
use App\Models\ProcurementRequest;
use App\Models\User;
use App\Services\ReferenceService;
use Illuminate\Http\Request;

class ProcurementRequestController extends Controller
{
    public function index(Request $request)
    {
        $org = currentOrganization();

        $query = ProcurementRequest::where('organization_id', $org->id)
            ->with(['department', 'requester', 'category'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $requests = $query->paginate(10)->withQueryString();
        $departments = Department::where('organization_id', $org->id)->orderBy('name')->get();
        $statuses = ['draft', 'submitted', 'approved', 'rejected'];
        $counts = [
            'total'     => ProcurementRequest::where('organization_id', $org->id)->count(),
            'pending'   => ProcurementRequest::where('organization_id', $org->id)->where('status', 'submitted')->count(),
            'approved'  => ProcurementRequest::where('organization_id', $org->id)->where('status', 'approved')->count(),
            'rejected'  => ProcurementRequest::where('organization_id', $org->id)->where('status', 'rejected')->count(),
        ];

        return view('requests.index', compact('requests', 'departments', 'statuses', 'counts'));
    }

    public function create()
    {
        $org = currentOrganization();

        $departments = Department::where('organization_id', $org->id)->orderBy('name')->get();
        $categories  = Category::where('organization_id', $org->id)->orderBy('name')->get();

        return view('requests.create', compact('departments', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['organization_id'] = currentOrganization()->id;
        $data['requester_id']    = auth()->id();
        $data['reference']       = ReferenceService::request(currentOrganization());
        $data['estimated_cost']  = collect($request->items)->sum(fn ($i) => ($i['quantity'] ?? 1) * ($i['estimated_unit_cost'] ?? 0));

        $pr = ProcurementRequest::create($data);

        foreach ($request->items as $item) {
            if (empty($item['description'])) {
                continue;
            }

            $qty = $item['quantity'] ?? 1;
            $cost = $item['estimated_unit_cost'] ?? 0;

            $pr->items()->create([
                'description'          => $item['description'],
                'quantity'             => $qty,
                'unit'                 => $item['unit'] ?? null,
                'estimated_unit_cost'  => $cost,
                'estimated_total'      => $qty * $cost,
            ]);
        }

        AuditLog::record('procurement_request_created', $pr, [], $pr->toArray());

        notifyUsers(
            User::where('organization_id', currentOrganization()->id)->whereIn('role', ['admin', 'approver'])->get(),
            'New procurement request',
            $pr->reference . ': ' . $pr->title,
            'info',
            route('requests.show', $pr)
        );

        return redirect()->route('requests.show', $pr)->with('success', 'Procurement request created.');
    }

    public function show(ProcurementRequest $request)
    {
        abort_unless($request->organization_id === currentOrganization()->id, 403);

        $request->load(['department', 'requester', 'category', 'approver', 'items', 'approvals.level']);

        $currentApproval = $request->approvals->where('status', 'pending')->sortBy('id')->first();
        $canApprove = false;

        if ($currentApproval && $currentApproval->level) {
            $canApprove = $this->userMatchesRole(auth()->user(), $currentApproval->level->role);
        } elseif (! $currentApproval && $request->status === 'submitted') {
            // No configured levels -> fallback to single-step approval by any approver
            $canApprove = auth()->user()->isApprover();
        }

        return view('requests.show', [
            'procurementRequest' => $request,
            'currentApproval'    => $currentApproval,
            'canApprove'         => $canApprove,
        ]);
    }

    public function edit(ProcurementRequest $request)
    {
        abort_unless($request->organization_id === currentOrganization()->id, 403);
        abort_unless($request->status === 'draft', 403);

        $org = currentOrganization();
        $departments = Department::where('organization_id', $org->id)->orderBy('name')->get();
        $categories  = Category::where('organization_id', $org->id)->orderBy('name')->get();

        return view('requests.edit', compact('request', 'departments', 'categories'));
    }

    public function update(Request $httpRequest, ProcurementRequest $request)
    {
        abort_unless($request->organization_id === currentOrganization()->id, 403);
        abort_unless($request->status === 'draft', 403);

        $data = $this->validateData($httpRequest);
        $data['estimated_cost'] = collect($httpRequest->items)->sum(fn ($i) => ($i['quantity'] ?? 1) * ($i['estimated_unit_cost'] ?? 0));

        $before = $request->toArray();
        $request->update($data);

        $request->items()->delete();
        foreach ($httpRequest->items as $item) {
            if (empty($item['description'])) {
                continue;
            }

            $qty = $item['quantity'] ?? 1;
            $cost = $item['estimated_unit_cost'] ?? 0;

            $request->items()->create([
                'description'          => $item['description'],
                'quantity'             => $qty,
                'unit'                 => $item['unit'] ?? null,
                'estimated_unit_cost'  => $cost,
                'estimated_total'      => $qty * $cost,
            ]);
        }

        AuditLog::record('procurement_request_updated', $request, $before, $request->toArray());

        return redirect()->route('requests.show', $request)->with('success', 'Procurement request updated.');
    }

    public function submit(ProcurementRequest $request)
    {
        abort_unless($request->organization_id === currentOrganization()->id, 403);
        abort_unless($request->status === 'draft', 403);

        $before = $request->toArray();
        $request->update(['status' => 'submitted']);

        AuditLog::record('procurement_request_submitted', $request, $before, $request->toArray());

        $levels = $this->createApprovalRecords($request);

        if ($levels->isEmpty()) {
            notifyUsers(
                User::where('organization_id', currentOrganization()->id)->whereIn('role', ['admin', 'approver'])->get(),
                'Procurement request pending approval',
                $request->reference . ': ' . $request->title,
                'warning',
                route('requests.show', $request)
            );

            return back()->with('success', 'Request submitted for approval.');
        }

        $current = $this->currentApproval($request);
        notifyUsers(
            $this->usersForRole($current?->level->role ?? 'approver'),
            'Procurement request pending approval',
            $request->reference . ': ' . $request->title . ' — ' . ($current?->level->name ?? 'Approval'),
            'warning',
            route('requests.show', $request)
        );

        return back()->with('success', 'Request submitted. Approval flow started.');
    }

    public function approve(Request $httpRequest, ProcurementRequest $request)
    {
        abort_unless($request->organization_id === currentOrganization()->id, 403);
        abort_unless($request->status === 'submitted', 403);

        $httpRequest->validate([
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $current = $this->currentApproval($request);

        // No configured levels -> single-step approval by any approver
        if (! $current) {
            abort_unless(auth()->user()->isApprover(), 403);

            $before = $request->toArray();
            $request->update([
                'status'      => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            AuditLog::record('procurement_request_approved', $request, $before, $request->toArray());

            return back()->with('success', 'Request approved.');
        }

        abort_unless($this->userMatchesRole(auth()->user(), $current->level->role), 403);

        $before = $current->toArray();
        $current->update([
            'status'     => 'approved',
            'approver_id'=> auth()->id(),
            'decided_at' => now(),
            'comment'    => $httpRequest->comment,
        ]);

        AuditLog::record('approval_level_completed', $request, $before, $current->toArray());

        $next = $this->currentApproval($request);

        if ($next) {
            notifyUsers(
                $this->usersForRole($next->level->role),
                'Next approval step',
                $request->reference . ': ' . $request->title . ' — ' . $next->level->name,
                'warning',
                route('requests.show', $request)
            );

            return back()->with('success', 'Level "' . $current->level->name . '" approved. Next: ' . $next->level->name);
        }

        $before = $request->toArray();
        $request->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        AuditLog::record('procurement_request_approved', $request, $before, $request->toArray());

        return back()->with('success', 'All approval levels complete. Request fully approved.');
    }

    public function reject(Request $httpRequest, ProcurementRequest $request)
    {
        abort_unless($request->organization_id === currentOrganization()->id, 403);
        abort_unless($request->status === 'submitted', 403);

        $httpRequest->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $current = $this->currentApproval($request);

        if ($current) {
            abort_unless($this->userMatchesRole(auth()->user(), $current->level->role), 403);

            $before = $current->toArray();
            $current->update([
                'status'     => 'rejected',
                'approver_id'=> auth()->id(),
                'decided_at' => now(),
                'comment'    => $httpRequest->rejection_reason,
            ]);

            AuditLog::record('approval_level_rejected', $request, $before, $current->toArray());
        } else {
            abort_unless(auth()->user()->isApprover(), 403);
        }

        // Mark all remaining pending levels as skipped
        $request->approvals()->where('status', 'pending')->update(['status' => 'skipped']);

        $before = $request->toArray();
        $request->update([
            'status'           => 'rejected',
            'rejection_reason' => $httpRequest->rejection_reason,
        ]);

        AuditLog::record('procurement_request_rejected', $request, $before, $request->toArray());

        return back()->with('success', 'Request rejected.');
    }

    public function destroy(ProcurementRequest $request)
    {
        abort_unless($request->organization_id === currentOrganization()->id, 403);
        abort_unless($request->status === 'draft', 403);

        AuditLog::record('procurement_request_removed', $request, $request->toArray(), []);
        $request->delete();

        return redirect()->route('requests.index')->with('success', 'Request removed.');
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'title'                => ['required', 'string', 'max:255'],
            'justification'        => ['nullable', 'string'],
            'department_id'        => ['nullable', 'exists:departments,id'],
            'category_id'          => ['nullable', 'exists:categories,id'],
            'budget_code'          => ['nullable', 'string', 'max:120'],
            'required_date'        => ['nullable', 'date'],
            'priority'             => ['nullable', 'in:low,normal,high,critical'],
            'currency'             => ['nullable', 'string', 'max:10'],
            'notes'                => ['nullable', 'string'],
            'items'                => ['required', 'array', 'min:1'],
        ]);
    }

    protected function createApprovalRecords(ProcurementRequest $request): \Illuminate\Support\Collection
    {
        $org = currentOrganization();
        $cost = (float) $request->estimated_cost;

        $levels = ApprovalLevel::where('organization_id', $org->id)
            ->where('is_active', true)
            ->where('min_amount', '<=', $cost)
            ->orderBy('sequence')
            ->get();

        foreach ($levels as $level) {
            $request->approvals()->create([
                'organization_id'   => $org->id,
                'approval_level_id' => $level->id,
                'status'            => 'pending',
            ]);
        }

        return $levels;
    }

    protected function currentApproval(ProcurementRequest $request): ?ApprovalRecord
    {
        return $request->approvals()->where('status', 'pending')->orderBy('id')->first();
    }

    protected function userMatchesRole(User $user, string $role): bool
    {
        return match ($role) {
            'admin'       => $user->isAdmin(),
            'approver'    => $user->isApprover(),
            'procurement' => $user->isProcurement(),
            'auditor'     => $user->isAuditor(),
            default       => $user->isApprover(),
        };
    }

    protected function usersForRole(string $role): \Illuminate\Database\Eloquent\Collection
    {
        $roles = match ($role) {
            'admin'       => ['admin'],
            'approver'    => ['admin', 'approver'],
            'procurement' => ['admin', 'procurement'],
            'auditor'     => ['admin', 'auditor'],
            default       => ['admin', 'approver'],
        };

        return User::where('organization_id', currentOrganization()->id)
            ->where('is_active', true)
            ->whereIn('role', $roles)
            ->get();
    }
}
