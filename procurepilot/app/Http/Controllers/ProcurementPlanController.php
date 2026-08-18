<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\ProcurementPlan;
use App\Models\ProcurementPlanItem;
use Illuminate\Http\Request;

class ProcurementPlanController extends Controller
{
    public function index(Request $request)
    {
        $org = currentOrganization();

        $query = ProcurementPlan::where('organization_id', $org->id)
            ->withCount('items')
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('fiscal_year')) {
            $query->where('fiscal_year', $request->fiscal_year);
        }

        $plans = $query->get();
        $fiscalYears = ProcurementPlan::where('organization_id', $org->id)->distinct()->pluck('fiscal_year');

        return view('plans.index', compact('plans', 'fiscalYears'));
    }

    public function create()
    {
        $categories = Category::where('organization_id', currentOrganization()->id)->orderBy('name')->get();
        $fiscalYears = collect([date('Y'), date('Y') + 1, date('Y') + 2]);

        return view('plans.create', compact('categories', 'fiscalYears'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'fiscal_year' => ['required', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
        ]);

        $data['organization_id'] = currentOrganization()->id;
        $data['created_by'] = auth()->id();

        $plan = ProcurementPlan::create($data);

        AuditLog::record('procurement_plan_created', $plan, [], $plan->toArray());

        return redirect()->route('plans.edit', $plan)->with('success', 'Procurement plan created. Add line items.');
    }

    public function show(ProcurementPlan $plan)
    {
        abort_unless($plan->organization_id === currentOrganization()->id, 403);

        $plan->load(['items.category', 'creator', 'approver']);

        return view('plans.show', compact('plan'));
    }

    public function edit(ProcurementPlan $plan)
    {
        abort_unless($plan->organization_id === currentOrganization()->id, 403);

        $categories = Category::where('organization_id', currentOrganization()->id)->orderBy('name')->get();
        $plan->load('items.category');

        return view('plans.edit', compact('plan', 'categories'));
    }

    public function update(Request $request, ProcurementPlan $plan)
    {
        abort_unless($plan->organization_id === currentOrganization()->id, 403);

        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'fiscal_year' => ['required', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
        ]);

        $before = $plan->toArray();
        $plan->update($data);

        AuditLog::record('procurement_plan_updated', $plan, $before, $plan->toArray());

        return back()->with('success', 'Plan updated.');
    }

    public function storeItem(Request $request, ProcurementPlan $plan)
    {
        abort_unless($plan->organization_id === currentOrganization()->id, 403);

        $data = $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'category_id'    => ['nullable', 'exists:categories,id'],
            'estimated_cost' => ['required', 'numeric', 'min:0'],
            'quantity'       => ['required', 'integer', 'min:1'],
            'method'         => ['required', 'in:open_competitive,restricted,single_source'],
            'priority'       => ['required', 'in:low,normal,high,critical'],
            'expected_date'  => ['nullable', 'date'],
        ]);

        $data['procurement_plan_id'] = $plan->id;

        ProcurementPlanItem::create($data);

        return back()->with('success', 'Line item added.');
    }

    public function destroyItem(ProcurementPlanItem $item)
    {
        abort_unless($item->plan->organization_id === currentOrganization()->id, 403);

        $item->delete();

        return back()->with('success', 'Line item removed.');
    }

    public function submit(ProcurementPlan $plan)
    {
        abort_unless($plan->organization_id === currentOrganization()->id, 403);

        $before = $plan->toArray();
        $plan->update(['status' => 'submitted']);

        AuditLog::record('procurement_plan_submitted', $plan, $before, $plan->toArray());

        return back()->with('success', 'Plan submitted for approval.');
    }

    public function approve(ProcurementPlan $plan)
    {
        abort_unless($plan->organization_id === currentOrganization()->id, 403);
        abort_unless(auth()->user()->isApprover(), 403);

        $before = $plan->toArray();
        $plan->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        AuditLog::record('procurement_plan_approved', $plan, $before, $plan->toArray());

        return back()->with('success', 'Procurement plan approved.');
    }

    public function reject(Request $request, ProcurementPlan $plan)
    {
        abort_unless($plan->organization_id === currentOrganization()->id, 403);
        abort_unless(auth()->user()->isApprover(), 403);

        $before = $plan->toArray();
        $plan->update(['status' => 'rejected', 'description' => $plan->description . "\n\nRejection note: " . ($request->note ?? '')]);

        AuditLog::record('procurement_plan_rejected', $plan, $before, $plan->toArray());

        return back()->with('success', 'Procurement plan rejected.');
    }

    public function destroy(ProcurementPlan $plan)
    {
        abort_unless($plan->organization_id === currentOrganization()->id, 403);

        AuditLog::record('procurement_plan_removed', $plan, $plan->toArray(), []);
        $plan->delete();

        return redirect()->route('plans.index')->with('success', 'Procurement plan removed.');
    }
}
