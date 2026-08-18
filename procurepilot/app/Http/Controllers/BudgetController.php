<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Budget;
use App\Models\Department;
use App\Models\ProcurementRequest;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $org = currentOrganization();

        $query = Budget::where('organization_id', $org->id)
            ->with('department')
            ->latest();

        if ($request->filled('fiscal_year')) {
            $query->where('fiscal_year', $request->fiscal_year);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $budgets = $query->paginate(10)->withQueryString();

        $totals = [
            'allocated' => Budget::where('organization_id', $org->id)->sum('allocated_amount'),
            'committed' => Budget::where('organization_id', $org->id)->sum('committed_amount'),
            'spent'     => Budget::where('organization_id', $org->id)->sum('spent_amount'),
        ];
        $totals['remaining'] = $totals['allocated'] - $totals['committed'] - $totals['spent'];

        $years = Budget::where('organization_id', $org->id)->distinct()->orderByDesc('fiscal_year')->pluck('fiscal_year');
        $statuses = ['draft', 'active', 'closed'];

        return view('budgets.index', compact('budgets', 'totals', 'years', 'statuses'));
    }

    public function create()
    {
        $departments = Department::where('organization_id', currentOrganization()->id)->where('is_active', true)->orderBy('name')->get();

        return view('budgets.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'fiscal_year'      => ['required', 'string', 'max:10'],
            'department_id'    => ['nullable', 'exists:departments,id'],
            'category'         => ['nullable', 'string', 'in:operating,capital,projects'],
            'allocated_amount' => ['required', 'numeric', 'min:0'],
            'currency'         => ['nullable', 'string', 'max:10'],
            'status'           => ['nullable', 'string', 'in:draft,active,closed'],
            'notes'            => ['nullable', 'string'],
        ]);

        $data['organization_id'] = currentOrganization()->id;
        $data['currency'] = $data['currency'] ?? 'NGN';
        $data['status'] = $data['status'] ?? 'active';

        $budget = Budget::create($data);

        AuditLog::record('budget_created', $budget, [], $budget->toArray());

        return redirect()->route('budgets.index')->with('success', 'Budget created.');
    }

    public function show(Budget $budget)
    {
        abort_unless($budget->organization_id === currentOrganization()->id, 403);

        $budget->load('department');

        $requests = ProcurementRequest::where('organization_id', $budget->organization_id)
            ->where('budget_code', $budget->name)
            ->orWhere(function ($q) use ($budget) {
                $q->where('organization_id', $budget->organization_id)->where('department_id', $budget->department_id);
            })
            ->with('requester')
            ->latest()
            ->take(10)
            ->get();

        return view('budgets.show', compact('budget', 'requests'));
    }

    public function edit(Budget $budget)
    {
        abort_unless($budget->organization_id === currentOrganization()->id, 403);

        $departments = Department::where('organization_id', currentOrganization()->id)->orderBy('name')->get();

        return view('budgets.edit', compact('budget', 'departments'));
    }

    public function update(Request $request, Budget $budget)
    {
        abort_unless($budget->organization_id === currentOrganization()->id, 403);

        $data = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'fiscal_year'      => ['required', 'string', 'max:10'],
            'department_id'    => ['nullable', 'exists:departments,id'],
            'category'         => ['nullable', 'string', 'in:operating,capital,projects'],
            'allocated_amount' => ['required', 'numeric', 'min:0'],
            'currency'         => ['nullable', 'string', 'max:10'],
            'status'           => ['nullable', 'string', 'in:draft,active,closed'],
            'notes'            => ['nullable', 'string'],
        ]);

        $before = $budget->toArray();
        $budget->update($data);

        AuditLog::record('budget_updated', $budget, $before, $budget->toArray());

        return redirect()->route('budgets.show', $budget)->with('success', 'Budget updated.');
    }

    public function commit(Request $request, Budget $budget)
    {
        abort_unless($budget->organization_id === currentOrganization()->id, 403);
        abort_unless(auth()->user()->isProcurement(), 403);

        $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $amount = (float) $request->amount;
        $remaining = $budget->remaining;

        if ($amount > $remaining) {
            return back()->withErrors(['Insufficient available budget balance. Remaining: ' . number_format($remaining, 2)]);
        }

        $before = $budget->toArray();
        $budget->update(['committed_amount' => (float) $budget->committed_amount + $amount]);

        AuditLog::record('budget_committed', $budget, $before, $budget->toArray());

        return back()->with('success', 'Amount committed against the budget.');
    }

    public function release(Budget $budget)
    {
        abort_unless($budget->organization_id === currentOrganization()->id, 403);
        abort_unless(auth()->user()->isProcurement(), 403);

        $before = $budget->toArray();
        $budget->update(['committed_amount' => 0]);

        AuditLog::record('budget_commitment_released', $budget, $before, $budget->toArray());

        return back()->with('success', 'Outstanding commitments released.');
    }

    public function destroy(Budget $budget)
    {
        abort_unless($budget->organization_id === currentOrganization()->id, 403);
        abort_unless((float) $budget->committed_amount + (float) $budget->spent_amount == 0, 403);

        AuditLog::record('budget_deleted', $budget, $budget->toArray(), []);
        $budget->delete();

        return redirect()->route('budgets.index')->with('success', 'Budget removed.');
    }
}
